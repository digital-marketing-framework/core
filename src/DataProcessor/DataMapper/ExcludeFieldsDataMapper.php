<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class ExcludeFieldsDataMapper extends DataMapper
{
    public const WEIGHT = 40;

    public const KEY_FIELDS = 'fields';

    public function mapData(DataInterface $target): DataInterface
    {
        $excludeFields = $this->getListConfig(static::KEY_FIELDS);
        foreach ($excludeFields as $excludeField) {
            if ($target->fieldExists($excludeField)) {
                unset($target[$excludeField]);
            }
        }

        return $target;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->getRenderingDefinition()->setSkipHeader(true);

        $listItemSchema = new StringSchema('fieldName');
        $listItemSchema->getRenderingDefinition()->setLabel('Field Name');
        $listSchema = new ListSchema($listItemSchema);
        $listSchema->getRenderingDefinition()->setLabel('Exclude Fields');
        $schema->addProperty(static::KEY_FIELDS, $listSchema);

        return $schema;
    }
}
