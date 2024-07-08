<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class FieldMapDataMapper extends DataMapper
{
    public const WEIGHT = 10;

    public const KEY_FIELDS = 'fields';

    public const DEFAULT_FIELDS = [];

    public function mapData(DataInterface $target): DataInterface
    {
        // TODO should we have a local field tracker that gets reset for every data mapper
        //      and a global field tracker that is shared between all data mappers?
        $baseContext = $this->context->copy(keepFieldTracker: true);
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
        $fieldMapKey->getRenderingDefinition()->addRole(RenderingDefinitionInterface::ROLE_OUTPUT_FIELD);
        $fieldMapKey->getSuggestedValues()->setContextual();
        $fieldMapValue = new CustomSchema(ValueSchema::TYPE);
        $fieldMap = new MapSchema($fieldMapValue, $fieldMapKey);
        $fieldMap->setDynamicOrder(true);
        $fieldMap->getRenderingDefinition()->setLabel('Field Map');
        $schema->addProperty(static::KEY_FIELDS, $fieldMap);

        return $schema;
    }
}
