<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\Icon;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

abstract class DataMapperGroup extends DataProcessorPlugin implements DataMapperGroupInterface
{
    abstract public function compute(): DataInterface;

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->getRenderingDefinition()->setIcon(Icon::DATA_MAPPER_GROUP);

        return $schema;
    }
}
