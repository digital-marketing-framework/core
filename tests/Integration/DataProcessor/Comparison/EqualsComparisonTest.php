<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\EqualsComparison;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers EqualsComparison
 */
class EqualsComparisonTest extends ComparisonTest
{
    protected const KEYWORD = 'equals';

    public function setUp(): void
    {
        parent::setUp();
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $this->data['field3'] = new MultiValue(['value3']);
        $this->data['field4'] = new MultiValue(['value4a', 'value4b']);
    }

    public function comparisonDataProvider(): array
    {
        return [
            [
                true,
                $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field1'], 'field'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
            ],
            [
                false,
                $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field1'], 'field'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value2'], 'constant'),
            ],
            [
                true,
                $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field3'], 'field'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value3'], 'constant'),
            ],
            [
                false,
                $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field3'], 'field'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value7'], 'constant'),
            ],
            [
                true,
                $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field4'], 'field'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value4b'], 'constant'),
            ],
            [
                true,
                $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field4'], 'field'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value4b'], 'constant'),
                'any',
            ],
            [
                false,
                $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field4'], 'field'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value4b'], 'constant'),
                'all',
            ],
            [
                true,
                $this->getValueConfiguration([FieldValueSource::KEY_FIELD_NAME => 'field3'], 'field'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value3'], 'constant'),
                'all',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider comparisonDataProvider
     */
    public function equals(bool $expectedResult, array $firstOperand, ?array $secondOperand = null, ?string $anyAll = null): void
    {
        $this->runComparisonTest($expectedResult, $firstOperand, $secondOperand, $anyAll);
    }
}