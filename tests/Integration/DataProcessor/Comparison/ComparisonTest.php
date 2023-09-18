<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContext;
use DigitalMarketingFramework\Core\DataProcessor\FieldTracker;
use DigitalMarketingFramework\Core\DataProcessor\FieldTrackerInterface;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataProcessorPluginTest;

abstract class ComparisonTest extends DataProcessorPluginTest
{
    /** @var array<string,string|ValueInterface|null> */
    protected array $data = [];

    /** @var array<int,array<string,mixed>> */
    protected array $configuration = [[]];

    protected FieldTrackerInterface $fieldTracker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fieldTracker = new FieldTracker();
    }

    /**
     * @param array<string,mixed> $config
     */
    protected function processComparison(array $config): bool
    {
        $dataProcessor = $this->registry->getDataProcessor();
        $context = new DataProcessorContext(new Data($this->data), new Configuration($this->configuration), $this->fieldTracker);

        return $dataProcessor->processComparison($config, $context);
    }

    /**
     * @param array<string,mixed> $firstOperand
     * @param ?array<string,mixed> $secondOperand
     */
    protected function runComparisonTest(bool $expectedResult, array $firstOperand, ?array $secondOperand = null, ?string $anyAll = null): void
    {
        $config = $this->getComparisonConfiguration($firstOperand, $secondOperand, $anyAll);
        $result = $this->processComparison($config);
        $this->assertEquals($expectedResult, $result);
    }
}
