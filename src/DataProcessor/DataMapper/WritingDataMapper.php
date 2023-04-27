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

    protected function addField(DataInterface $data, string $fieldName, string|null|ValueInterface $value): void
    {
        if ($value !== null && ($this->getConfig(static::KEY_OVERWRITE) || $data->fieldEmpty($fieldName))) {
            $data[$fieldName] = $value;
        }
    }

    public static function getDefaultConfiguration(?bool $enabled = null): array
    {
        return parent::getDefaultConfiguration($enabled) + [
            static::KEY_OVERWRITE => static::DEFAULT_OVERWRITE,
        ];
    }
    
    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_OVERWRITE, new BooleanSchema());
        return $schema;
    }
}
