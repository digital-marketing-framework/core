<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Stream;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPluginInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface StreamInterface extends DataProcessorPluginInterface
{
    public function compute(): DataInterface;
}
