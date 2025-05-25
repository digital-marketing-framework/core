<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\InComparison;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class InComparisonTest extends ComparisonTestBase
{
    protected const CLASS_NAME = InComparison::class;

    protected const KEYWORD = 'in';

    /**
     * @return array<array{0:bool,1:array<string,mixed>,2:mixed,3?:?array<string,mixed>,4?:mixed,5?:?string}>
     */
    public static function comparisonDataProvider(): array
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
                ['value1', 'value2'],
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
                ['value1', 'value2'],
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1', 'value3'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                ['value1', 'value2'],
            ],
            [
                true,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1', 'value3'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                ['value1', 'value2'],
                'any',
            ],
            [
                false,
                ['firstOperandConfigKey' => 'firstOperandConfigValue'],
                ['value1', 'value3'],
                ['secondOperandConfigKey' => 'secondOperandConfigValue'],
                ['value1', 'value2'],
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
    public function in(bool $expectedResult, array $firstOperand, mixed $firstOperandResult, ?array $secondOperand = null, mixed $secondOperandResult = null, ?string $anyAll = null): void
    {
        $this->runComparisonTest($expectedResult, $firstOperand, $firstOperandResult, $secondOperand, $secondOperandResult, $anyAll);
    }
}
