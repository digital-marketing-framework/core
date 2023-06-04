<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class ExcludeFieldsDataMapper extends DataMapper
{
    public const WEIGHT = 40;

    public const KEY_FIELDS = 'fields';
    public const DEFAULT_FIELDS = '';

    protected function map(DataInterface $target)
    {
        $excludeFields = GeneralUtility::castValueToArray($this->getConfig(static::KEY_FIELDS));
        foreach ($excludeFields as $excludeField) {
            if ($target->fieldExists($excludeField)) {
                unset($target[$excludeField]);
            }
        }
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_FIELDS, new StringSchema());
        return $schema;
    }
}
