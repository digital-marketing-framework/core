<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\BinaryComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\Comparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessor;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataProcessorPluginTest;

abstract class ComparisonTest extends DataProcessorPluginTest
{
    protected ComparisonInterface $subject;

    protected function processComparison(array $config): bool
    {
        $class = static::CLASS_NAME;
        $this->subject = new $class(static::KEYWORD, $this->registry, $config, $this->getContext());
        $this->subject->setDataProcessor($this->dataProcessor);
        return $this->subject->compare();
    }

    protected function runComparisonTest(bool $expectedResult, array $firstOperand, mixed $firstOperandResult, ?array $secondOperand = null, mixed $secondOperandResult = null, ?string $anyAll = null): void
    {
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
            DataProcessor::KEY_TYPE => static::KEYWORD,
            BinaryComparison::KEY_FIRST_OPERAND => $firstOperand,
        ];
        if ($secondOperand !== null) {
            $config[BinaryComparison::KEY_SECOND_OPERAND] = $secondOperand;
        }
        if ($anyAll !== null) {
            $config[Comparison::KEY_ANY_ALL] = $anyAll;
        }
        $result = $this->processComparison($config);
        $this->assertEquals($expectedResult, $result);
    }
}
