<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\InsertDataValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifier;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use PHPUnit\Framework\Attributes\Test;

class InsertDataValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'insertData';

    protected const CLASS_NAME = InsertDataValueModifier::class;

    protected const ENABLED = [ValueModifier::KEY_ENABLED => true];

    public const MODIFY_TEST_CASES = [
        [null,                    null],
        ['',                      ''],
        ['{field1}',              'value1'],
        ['{field1}-{field2}',     'value1-value2'],
        ['{field4}',              ''],
        ['{multiValue}',          ['a', 'b', 'c']],
        ['-{multiValue}',         '-a,b,c'],
        ['{field2}-{multiValue}', 'value2-a,b,c'],
        ['{field1}{field4}',      'value1'],

        // field names are not restricted to a character class
        ['{field.with.dots}',     'dotted'],
        ['[{field.with.dots}]',   '[dotted]'],
        ['{field with spaces}',   'spaced'],

        // variables
        ['${campaignCode}',                'PPC-EN-JUNE19'],
        ['code=${campaignCode}',           'code=PPC-EN-JUNE19'],
        ['${emptyVariable}',               ''],
        ['${undeclaredVariable}',          ''],
        ['${campaignCode}/{field1}',       'PPC-EN-JUNE19/value1'],

        // a field and a variable sharing a name must not interfere:
        // "{collision}" is a substring of "${collision}"
        ['{collision}',           'fieldValue'],
        ['${collision}',          'variableValue'],
        ['{collision}${collision}', 'fieldValuevariableValue'],
        ['${collision}{collision}', 'variableValuefieldValue'],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $this->data['field3'] = 'value3';
        $this->data['multiValue'] = new MultiValue(['a', 'b', 'c']);
        $this->data['field.with.dots'] = 'dotted';
        $this->data['field with spaces'] = 'spaced';
        $this->data['collision'] = 'fieldValue';

        $this->configuration[0][ConfigurationInterface::KEY_DATA_PROCESSING][ConfigurationInterface::KEY_VARIABLES] = [
            'id.v1' => $this->createMapItem('campaignCode', 'PPC-EN-JUNE19', 'id.v1', 10),
            'id.v2' => $this->createMapItem('emptyVariable', '', 'id.v2', 20),
            'id.v3' => $this->createMapItem('collision', 'variableValue', 'id.v3', 30),
        ];
    }

    public static function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }

    /**
     * A lone field reference returns the field value itself so that multi-value fields
     * survive; a lone variable reference is always a plain string.
     */
    #[Test]
    public function wholeValueFieldKeepsValueObjectWhileVariableDoesNot(): void
    {
        $this->assertInstanceOf(MultiValue::class, $this->processValueModifier(static::ENABLED, '{multiValue}'));
        $this->assertSame('PPC-EN-JUNE19', $this->processValueModifier(static::ENABLED, '${campaignCode}'));
    }

    /**
     * Field placeholders are tracked as processed, so that FieldCollectorValueSource does
     * not report them as unprocessed. Variable placeholders are not fields and must not
     * touch the tracker.
     */
    #[Test]
    public function fieldPlaceholdersAreMarkedAsProcessedAndVariablesAreNot(): void
    {
        $this->processValueModifier(static::ENABLED, '{field1} ${campaignCode}');

        $this->assertTrue($this->fieldTracker->hasBeenProcessed('field1'));
        $this->assertFalse($this->fieldTracker->hasBeenProcessed('campaignCode'));
    }

    /**
     * An undeclared variable is a configuration error and is logged. A missing field is a
     * routine case - an optional field that was not submitted - and is not.
     */
    #[Test]
    public function onlyUndeclaredVariablesAreLogged(): void
    {
        $this->logger = $this->createLoggerMock();
        $this->logger->expects($this->once())->method('warning')
            ->with($this->stringContains('undeclaredVariable'));

        $this->assertSame('--', $this->processValueModifier(static::ENABLED, '-{unknownField}-${undeclaredVariable}'));
    }

    #[Test]
    public function missingFieldsAreNotLogged(): void
    {
        $this->logger = $this->createLoggerMock();
        $this->logger->expects($this->never())->method('warning');

        $this->assertSame('-', $this->processValueModifier(static::ENABLED, '-{unknownField}'));
    }
}
