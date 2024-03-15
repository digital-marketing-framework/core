<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Stream;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

abstract class Stream extends DataProcessorPlugin implements StreamInterface
{
    abstract public function compute(): DataInterface;

    public static function getSchema(): SchemaInterface
    {
        return new ContainerSchema();
    }
}
