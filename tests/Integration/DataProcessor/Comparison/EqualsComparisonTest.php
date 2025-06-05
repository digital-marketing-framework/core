<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\EqualsComparison;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(EqualsComparison::class)]
class EqualsComparisonTest extends ComparisonTestBase
{
    protected const KEYWORD = 'equals';

    protected function setUp(): void
    {
        parent::setUp();
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $this->data['field3'] = new MultiValue(['value3']);
        $this->data['field4'] = new MultiValue(['value4a', 'value4b']);
    }

    /**
     * @return array<array{0:bool,1:array<string,mixed>,2?:?array<string,mixed>,3?:?string}>
     */
    public static function comparisonDataProvider(): array
    {
        return [
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field1'], 'field'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
            ],
            [
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field1'], 'field'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value2'], 'constant'),
            ],
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field3'], 'field'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value3'], 'constant'),
            ],
            [
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field3'], 'field'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value7'], 'constant'),
            ],
            [
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field4'], 'field'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value4b'], 'constant'),
            ],
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field4'], 'field'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value4b'], 'constant'),
                'any',
            ],
            [
                false,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field4'], 'field'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value4b'], 'constant'),
                'all',
            ],
            [
                true,
                static::getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field3'], 'field'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value3'], 'constant'),
                'all',
            ],
        ];
    }

    /**
     * @param array<string,mixed> $firstOperand
     * @param ?array<string,mixed> $secondOperand
     */
    #[Test]
    #[DataProvider('comparisonDataProvider')]
    public function equals(bool $expectedResult, array $firstOperand, ?array $secondOperand = null, ?string $anyAll = null): void
    {
        $this->runComparisonTest($expectedResult, $firstOperand, $secondOperand, $anyAll);
    }
}
