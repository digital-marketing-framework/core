<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\BinaryComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\Comparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataProcessorPluginTest;

abstract class ComparisonTest extends DataProcessorPluginTest
{
    protected const DEFAULT_CONFIG = [
        Comparison::KEY_ANY_ALL => Comparison::VALUE_ANY_ALL_ANY,
    ];

    protected ComparisonInterface $subject;

    /**
     * @param array<string,mixed> $config
     * @param array<string,mixed> $defaultConfig
     */
    protected function processComparison(array $config, array $defaultConfig): bool
    {
        $class = static::CLASS_NAME;
        $this->subject = new $class(static::KEYWORD, $this->registry, $config, $this->getContext());
        $this->subject->setDataProcessor($this->dataProcessor);
        $this->subject->setDefaultConfiguration($defaultConfig);

        return $this->subject->compare();
    }

    /**
     * @param array<string,mixed> $firstOperand
     * @param ?array<string,mixed> $secondOperand
     * @param ?array<string,mixed> $defaultConfig
     */
    protected function runComparisonTest(
        bool $expectedResult,
        array $firstOperand,
        mixed $firstOperandResult,
        ?array $secondOperand = null,
        mixed $secondOperandResult = null,
        ?string $anyAll = null,
        ?array $defaultConfig = null
    ): void {
        if ($defaultConfig === null) {
            $defaultConfig = static::DEFAULT_CONFIG;
        }

        $with = [
            [$firstOperand],
        ];
        $results = [
            $this->convertMultiValues($firstOperandResult),
        ];
        if ($secondOperand !== null) {
            $with[] = [$secondOperand];
            $results[] = $this->convertMultiValues($secondOperandResult);
        }

        $this->dataProcessor->method('processValue')->withConsecutive(...$with)->willReturnOnConsecutiveCalls(...$results);
        $config = [
            Comparison::KEY_OPERATION => static::KEYWORD,
            BinaryComparison::KEY_FIRST_OPERAND => $firstOperand,
        ];
        if ($secondOperand !== null) {
            $config[BinaryComparison::KEY_SECOND_OPERAND] = $secondOperand;
        }

        if ($anyAll !== null) {
            $config[Comparison::KEY_ANY_ALL] = $anyAll;
        }

        $result = $this->processComparison($config, $defaultConfig);
        $this->assertEquals($expectedResult, $result);
    }
}
