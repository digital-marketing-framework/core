<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContext;
use DigitalMarketingFramework\Core\DataProcessor\FieldTracker;
use DigitalMarketingFramework\Core\DataProcessor\FieldTrackerInterface;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataProcessorPluginTest;

abstract class ValueSourceTest extends DataProcessorPluginTest
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
    protected function processValueSource(array $config): string|null|ValueInterface
    {
        $dataProcessor = $this->registry->getDataProcessor();
        $context = new DataProcessorContext(new Data($this->data), new Configuration($this->configuration), $this->fieldTracker);

        return $dataProcessor->processValueSource($config, $context);
    }
}
