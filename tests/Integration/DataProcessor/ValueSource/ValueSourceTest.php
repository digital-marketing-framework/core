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
    protected array $data = [];
    protected array $configuration = [[]];
    protected FieldTrackerInterface $fieldTracker;

    public function setUp(): void
    {
        parent::setUp();
        $this->fieldTracker = new FieldTracker();
    }

    protected function processValueSource(array $config): string|null|ValueInterface
    {
        $dataProcessor = $this->registry->getDataProcessor();
        $context = new DataProcessorContext(new Data($this->data), new Configuration($this->configuration), $this->fieldTracker);
        return $dataProcessor->processValueSource($config, $context);
    }
}
