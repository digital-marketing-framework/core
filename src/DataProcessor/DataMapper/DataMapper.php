<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

abstract class DataMapper extends DataProcessorPlugin implements DataMapperInterface
{
    abstract public function mapData(DataInterface $target): DataInterface;

    protected function addField(DataInterface $data, string $fieldName, string|null|ValueInterface $value, bool $overwrite = false): bool
    {
        if ($value !== null && ($overwrite || $data->fieldEmpty($fieldName))) {
            $data[$fieldName] = $value;

            return true;
        }

        return false;
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->getRenderingDefinition()->setNavigationItem(false);

        return $schema;
    }
}
