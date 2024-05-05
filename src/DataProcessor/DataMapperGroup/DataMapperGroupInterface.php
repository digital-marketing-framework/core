<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPluginInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface DataMapperGroupInterface extends DataProcessorPluginInterface
{
    public function compute(): DataInterface;
}
