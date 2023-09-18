<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface DataProcessorContextInterface
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(): array;

    public function getFieldTracker(): FieldTrackerInterface;

    public function getData(): DataInterface;

    public function getConfiguration(): ConfigurationInterface;

    public function copy(bool $keepFieldTracker = true, ?DataInterface $data = null): DataProcessorContextInterface;
}
