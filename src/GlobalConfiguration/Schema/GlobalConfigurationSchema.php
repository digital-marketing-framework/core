<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration\Schema;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;

abstract class GlobalConfigurationSchema extends ContainerSchema implements GlobalConfigurationSchemaInterface
{
    public function getWeight(): int
    {
        return 100;
    }
}
