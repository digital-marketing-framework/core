<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsTrueComparison;

class IsTrueComparisonTest extends ComparisonTest
{
    protected const CLASS_NAME = IsTrueComparison::class;

    protected const KEYWORD = 'isTrue';

    /**
     * @return array<array{0:bool,1:array<string,mixed>,2:mixed,3?:?string}>
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
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                '',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                '0',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                null,
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1'],
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                [],
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                [''],
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['', '1'],
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['', '1'],
                'any',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['', '1'],
                'all',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['0'],
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['0', '1'],
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['0', '1'],
                'any',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['0', '1'],
                'all',
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
    public function isTrueTest(bool $expectedResult, array $firstOperand, mixed $firstOperandResult, ?string $anyAll = null): void
    {
        $this->runComparisonTest($expectedResult, $firstOperand, $firstOperandResult, null, null, $anyAll);
    }
}
