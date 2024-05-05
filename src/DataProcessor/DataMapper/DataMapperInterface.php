<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPluginInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

interface DataMapperInterface extends DataProcessorPluginInterface
{
    public function mapData(DataInterface $target): DataInterface;
}
