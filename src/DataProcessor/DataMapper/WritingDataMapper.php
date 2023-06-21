<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

abstract class WritingDataMapper extends DataMapper
{
    public const KEY_OVERWRITE = 'overwrite';
    public const DEFAULT_OVERWRITE = false;

    protected function addField(DataInterface $data, string $fieldName, string|null|ValueInterface $value): bool
    {
        if ($value !== null && ($this->getConfig(static::KEY_OVERWRITE) || $data->fieldEmpty($fieldName))) {
            $data[$fieldName] = $value;
            return true;
        }
        return false;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_OVERWRITE, new BooleanSchema());
        return $schema;
    }
}
