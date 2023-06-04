<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

abstract class DataMapper extends DataProcessorPlugin implements DataMapperInterface
{

    public const KEY_ENABLED = 'enabled';
    public const DEFAULT_ENABLED = false;

    protected function proceed(): bool
    {
        return $this->getConfig(static::KEY_ENABLED);
    }

    abstract protected function map(DataInterface $target);

    public function mapData(DataInterface $target): DataInterface
    {
        if ($this->proceed()) {
            $this->map($target);
        }
        return $target;
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->getRenderingDefinition()->setNavigationItem(false);
        $schema->addProperty(static::KEY_ENABLED, new BooleanSchema(false));
        return $schema;
    }
}
