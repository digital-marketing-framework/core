<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapperGroup;

use DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup\SingleDataMapperGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

/**
 * Field tracking within one data mapper group: the field map marks what it reads, and the
 * passthrough and field collector mechanisms of the same group act on what is left.
 */
#[CoversClass(SingleDataMapperGroup::class)]
class SingleDataMapperGroupTest extends DataMapperGroupTestBase
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

    #[Test]
    public function passthroughSkipsFieldsTheFieldMapRead(): void
    {
        $output = $this->processDataMapperGroup(static::singleGroup(
            static::fieldMap(['contact_email' => static::fieldValue('email')])
            + static::passthrough()
        ));

        static::assertMultiValueEquals([
            'contact_email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '12345',
        ], $output);
    }

    #[Test]
    public function fieldCollectorSkipsFieldsReadEarlierInTheSameFieldMap(): void
    {
        $output = $this->processDataMapperGroup(static::singleGroup(
            static::fieldMap([
                'contact_email' => static::fieldValue('email'),
                'leftovers' => static::collectedFields(),
            ])
        ));

        static::assertMultiValueEquals(['first_name', 'last_name', 'phone'], $output['leftovers']);
    }

    #[Test]
    public function passthroughIncludeFieldsOverridesTheTracker(): void
    {
        $output = $this->processDataMapperGroup(static::singleGroup(
            static::fieldMap(['contact_email' => static::fieldValue('email')])
            + static::passthrough(unprocessedOnly: true, includeFields: 'email')
        ));

        static::assertMultiValueEquals([
            'contact_email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '12345',
        ], $output);
    }

    #[Test]
    public function fieldCollectorIncludeOverridesTheTracker(): void
    {
        $output = $this->processDataMapperGroup(static::singleGroup(
            static::fieldMap([
                'contact_email' => static::fieldValue('email'),
                'leftovers' => static::collectedFields(include: 'email'),
            ])
        ));

        static::assertMultiValueEquals(['first_name', 'last_name', 'email', 'phone'], $output['leftovers']);
    }
}
