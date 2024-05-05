<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

abstract class DataMapper extends DataProcessorPlugin implements DataMapperInterface
{
    abstract public function mapData(DataInterface $target): DataInterface;

    protected function addField(DataInterface $data, string $fieldName, string|ValueInterface|null $value, bool $overwrite = false): bool
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
