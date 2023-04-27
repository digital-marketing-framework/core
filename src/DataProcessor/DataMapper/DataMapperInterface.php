<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface DataMapperInterface extends PluginInterface
{
    public function mapData(DataInterface $target): DataInterface;

    public static function getDefaultConfiguration(?bool $enabled = null): array;
}
