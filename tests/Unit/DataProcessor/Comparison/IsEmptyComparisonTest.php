<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsEmptyComparison;

class IsEmptyComparisonTest extends ComparisonTest
{
    protected const CLASS_NAME = IsEmptyComparison::class;

    protected const KEYWORD = 'isEmpty';

    /**
     * @return array<array{0:bool,1:array<string,mixed>,2:mixed}>
     */
    public function comparisonDataProvider(): array
    {
        return [
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                'value1',
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                '',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                '0',
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                null,
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1'],
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                [],
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                [''],
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['0'],
            ],
        ];
    }

    /**
     * @param array<string,mixed> $firstOperand
     *
     * @test
     *
     * @dataProvider comparisonDataProvider
     */
    public function isEmptyTest(bool $expectedResult, array $firstOperand, mixed $firstOperandResult): void
    {
        $this->runComparisonTest($expectedResult, $firstOperand, $firstOperandResult);
    }
}
