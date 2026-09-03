<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapperGroup;

use DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup\SequenceDataMapperGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

/**
 * Field tracking across a sequence: the tracker is scoped to a single data mapper group, so
 * marks made by one group of a sequence are not visible to the next one.
 */
#[CoversClass(SequenceDataMapperGroup::class)]
class SequenceDataMapperGroupTest extends DataMapperGroupTestBase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '12345',
        ];
    }

    /**
     * Reads "email" and passes every field on unchanged, so that the second group receives
     * a field that the first group has already marked as processed.
     */
    protected function registerNormalizeGroup(): void
    {
        $this->registerDataMapperGroup('normalize', static::singleGroup(
            static::fieldMap(['email' => static::fieldValue('email')])
            + static::passthrough(unprocessedOnly: false)
        ));
    }

    #[Test]
    public function passthroughIgnoresMarksMadeByAPreviousGroup(): void
    {
        $this->registerNormalizeGroup();

        // Reads "phone" and nothing else, so "email" is untouched as far as this group knows.
        $this->registerDataMapperGroup('deliver', static::singleGroup(
            static::fieldMap(['contact_phone' => static::fieldValue('phone')])
            + static::passthrough()
        ));

        $output = $this->processDataMapperGroup(static::sequenceGroup(['normalize', 'deliver']));

        static::assertMultiValueEquals([
            'contact_phone' => '12345',
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ], $output);
    }

    #[Test]
    public function fieldCollectorIgnoresMarksMadeByAPreviousGroup(): void
    {
        $this->registerNormalizeGroup();

        $this->registerDataMapperGroup('collect', static::singleGroup(
            static::fieldMap(['leftovers' => static::collectedFields()])
        ));

        $output = $this->processDataMapperGroup(static::sequenceGroup(['normalize', 'collect']));

        static::assertMultiValueEquals(['email', 'first_name', 'last_name', 'phone'], $output['leftovers']);
    }

    /**
     * The accepted trade-off of the group-scoped tracker: a group that folds fields into another
     * one and still passes the source fields on cannot tell the next group that they are spent.
     */
    #[Test]
    public function foldedSourceFieldsResurfaceAsLeftoversInTheNextGroup(): void
    {
        $this->registerDataMapperGroup('combine', static::singleGroup(
            static::fieldMap(['name' => static::concatenatedFields('first_name', 'last_name')])
            + static::passthrough(unprocessedOnly: false)
        ));
        $this->registerDataMapperGroup('collect', static::singleGroup(
            static::fieldMap(['leftovers' => static::collectedFields()])
        ));

        $output = $this->processDataMapperGroup(static::sequenceGroup(['combine', 'collect']));

        static::assertMultiValueEquals(['name', 'first_name', 'last_name', 'email', 'phone'], $output['leftovers']);
    }

    /**
     * First remedy for the trade-off above: the group that consumes the fields drops them from
     * its own output, so the next group never sees them.
     */
    #[Test]
    public function consumingGroupCanDropTheFieldsItFoldedIn(): void
    {
        $this->registerDataMapperGroup('combine', static::singleGroup(
            static::fieldMap(['name' => static::concatenatedFields('first_name', 'last_name')])
            + static::passthrough(unprocessedOnly: true)
        ));
        $this->registerDataMapperGroup('collect', static::singleGroup(
            static::fieldMap(['leftovers' => static::collectedFields()])
        ));

        $output = $this->processDataMapperGroup(static::sequenceGroup(['combine', 'collect']));

        static::assertMultiValueEquals(['name', 'email', 'phone'], $output['leftovers']);
    }

    /**
     * Second remedy: the receiving group states which fields it does not want.
     */
    #[Test]
    public function receivingGroupCanExcludeTheFieldsItDoesNotWant(): void
    {
        $this->registerDataMapperGroup('combine', static::singleGroup(
            static::fieldMap(['name' => static::concatenatedFields('first_name', 'last_name')])
            + static::passthrough(unprocessedOnly: false)
        ));
        $this->registerDataMapperGroup('collect', static::singleGroup(
            static::fieldMap(['leftovers' => static::collectedFields(exclude: 'first_name,last_name')])
        ));

        $output = $this->processDataMapperGroup(static::sequenceGroup(['combine', 'collect']));

        static::assertMultiValueEquals(['name', 'email', 'phone'], $output['leftovers']);
    }
}
