<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\RegExpComparison;

class RegExpComparisonTest extends ComparisonTest
{
    protected const CLASS_NAME = RegExpComparison::class;

    protected const KEYWORD = 'regExp';

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
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                'value1',
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                'lue1',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                'value1',
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                '^lue1',
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                'value1',
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                'lue1$',
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
                'lue2',
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
                ['value1', 'value2'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                '^value[12]$',
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
    public function regExp(bool $expectedResult, array $firstOperand, mixed $firstOperandResult, ?array $secondOperand = null, mixed $secondOperandResult = null, ?string $anyAll = null): void
    {
        $this->runComparisonTest($expectedResult, $firstOperand, $firstOperandResult, $secondOperand, $secondOperandResult, $anyAll);
    }
}
