<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\ExistsComparison;

class ExistsComparisonTest extends ComparisonTest
{
    protected const CLASS_NAME = ExistsComparison::class;

    protected const KEYWORD = 'exists';

    /**
     * @return array<array{0:bool,1:array<string,mixed>,2:mixed}>
     */
    public function comparisonDataProvider(): array
    {
        return [
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                'value1',
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                '',
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                '0',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                null,
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
    public function exists(bool $expectedResult, array $firstOperand, mixed $firstOperandResult): void
    {
        $this->runComparisonTest($expectedResult, $firstOperand, $firstOperandResult);
    }
}
