<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class FieldsDataMapper extends WritingDataMapper
{
    public const KEY_FIELDS = 'fields';
    public const DEFAULT_FIELDS = [];

    protected function map(DataInterface $target)
    {
        $baseContext = $this->context->copy(false);
        foreach ($this->getConfig(static::KEY_FIELDS) as $fieldName => $valueConfig) {
            $context = $baseContext->copy();
            $value = $this->dataProcessor->processValue($valueConfig, $context);
            if ($value !== null) {
                $this->addField($target, $fieldName, $value);
            }
        }
    }

    public static function getDefaultConfiguration(?bool $enabled = null): array
    {
        return parent::getDefaultConfiguration($enabled) + [
            static::KEY_FIELDS => static::DEFAULT_FIELDS,
        ];
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_FIELDS, new MapSchema(new CustomSchema(ValueSchema::TYPE)));
        return $schema;
    }
}
