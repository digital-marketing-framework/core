<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\InComparison;

class InComparisonTest extends ComparisonTest
{
    protected const CLASS_NAME = InComparison::class;
    protected const KEYWORD = 'in';

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
                'value1',
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                'value1,value2',
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                'value1',
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                ['value1','value2'],
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                ['value1'],
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                ['value1','value2'],
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1', 'value3'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                ['value1','value2'],
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1', 'value3'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                ['value1','value2'],
                'any',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1', 'value3'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                ['value1','value2'],
                'all',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider comparisonDataProvider
     */
    public function in(bool $expectedResult, array $firstOperand, mixed $firstOperandResult, ?array $secondOperand = null, mixed $secondOperandResult = null, ?string $anyAll = null): void
    {
        $this->runComparisonTest($expectedResult, $firstOperand, $firstOperandResult, $secondOperand, $secondOperandResult, $anyAll);
    }
}
