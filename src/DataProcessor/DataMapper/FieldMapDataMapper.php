<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class FieldMapDataMapper extends DataMapper
{
    public const WEIGHT = 10;

    public const KEY_FIELDS = 'fields';

    public const DEFAULT_FIELDS = [];

    public function mapData(DataInterface $target): DataInterface
    {
        $baseContext = $this->context->copy(false);
        foreach ($this->getMapConfig(static::KEY_FIELDS) as $fieldName => $valueConfig) {
            $context = $baseContext->copy();
            $value = $this->dataProcessor->processValue($valueConfig, $context);
            if ($value !== null) {
                $this->addField($target, $fieldName, $value);
            }
        }

        return $target;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->getRenderingDefinition()->setSkipHeader(true);

        $fieldMapKey = new StringSchema('fieldName');
        $fieldMapKey->getRenderingDefinition()->setLabel('Target Field Name');
        $fieldMapValue = new CustomSchema(ValueSchema::TYPE);
        $fieldMap = new MapSchema($fieldMapValue, $fieldMapKey);
        $fieldMap->setDynamicOrder(true);
        $fieldMap->getRenderingDefinition()->setLabel('Field Map');
        $schema->addProperty(static::KEY_FIELDS, $fieldMap);

        return $schema;
    }
}
