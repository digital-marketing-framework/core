<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsFalseComparison;

class IsFalseComparisonTest extends ComparisonTest
{
    protected const CLASS_NAME = IsFalseComparison::class;

    protected const KEYWORD = 'isFalse';

    /**
     * @return array<array{0:bool,1:array<string,mixed>,2:mixed,3?:?string}>
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
                true,
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
                true,
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
                true,
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
    public function isFalseTest(bool $expectedResult, array $firstOperand, mixed $firstOperandResult, ?string $anyAll = null): void
    {
        $this->runComparisonTest($expectedResult, $firstOperand, $firstOperandResult, null, null, $anyAll);
    }
}
