<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IgnoreEmptyFieldsDataMapper extends DataMapper
{
    public const WEIGHT = 30;

    public const KEY_ENABLED = 'enabled';

    public const DEFAULT_ENABLED = true;

    public function mapData(DataInterface $target): DataInterface
    {
        if (!$this->getConfig(static::KEY_ENABLED)) {
            return $target;
        }

        $toDeleteList = [];
        foreach ($target as $fieldName => $value) {
            if (GeneralUtility::isEmpty($value)) {
                $toDeleteList[] = $fieldName;
            }
        }

        foreach ($toDeleteList as $toDelete) {
            unset($target[$toDelete]);
        }

        return $target;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->getRenderingDefinition()->setSkipHeader(true);

        $enabledSchema = new BooleanSchema(static::DEFAULT_ENABLED);
        $enabledSchema->getRenderingDefinition()->setLabel('Ignore Empty Fields');
        $schema->addProperty(static::KEY_ENABLED, $enabledSchema);

        return $schema;
    }
}
