<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\EqualsComparison;

class EqualsComparisonTest extends ComparisonTest
{
    protected const CLASS_NAME = EqualsComparison::class;

    protected const KEYWORD = 'equals';

    /**
     * @return array<array{0:bool,1:array<string,mixed>,2:mixed,3?:?array<string,mixed>,4?:mixed,5?:?string}>
     */
    public function comparisonDataProvider(): array
    {
        return [
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                'value1',
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                'value1',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                'value1',
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                'value2',
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                'value1',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                'value2',
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1', 'value2'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                'value2',
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1', 'value2'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                'value2',
                'any',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1', 'value2'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                'value2',
                'all',
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                'value1',
                'all',
            ],
        ];
    }

    /**
     * @param array<string,mixed> $firstOperand
     * @param ?array<string,mixed> $secondOperand
     *
     * @test
     *
     * @dataProvider comparisonDataProvider
     */
    public function equals(
        bool $expectedResult,
        array $firstOperand,
        mixed $firstOperandResult,
        ?array $secondOperand = null,
        mixed $secondOperandResult = null,
        ?string $anyAll = null
    ): void {
        $this->runComparisonTest($expectedResult, $firstOperand, $firstOperandResult, $secondOperand, $secondOperandResult, $anyAll);
    }
}
