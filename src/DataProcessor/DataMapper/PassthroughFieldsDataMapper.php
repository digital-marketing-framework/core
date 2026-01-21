<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class PassthroughFieldsDataMapper extends DataMapper
{
    public const WEIGHT = 20;

    public const KEY_ENABLED = 'enabled';

    public const DEFAULT_ENABLED = false;

    public const KEY_UNPROCESSED_ONLY = 'unprocessedOnly';

    public const DEFAULT_UNPROCESSED_ONLY = false;

    public const KEY_INCLUDE_FIELDS = 'includeFields';

    public const DEFAULT_INCLUDE_FIELDS = '';

    public function mapData(DataInterface $target): DataInterface
    {
        if (!$this->getBoolConfig(static::KEY_ENABLED)) {
            return $target;
        }

        $unprocessedOnly = $this->getBoolConfig(static::KEY_UNPROCESSED_ONLY);
        $includeFields = GeneralUtility::castValueToArray($this->getConfig(static::KEY_INCLUDE_FIELDS));

        foreach ($this->context->getData() as $fieldName => $value) {
            if ($unprocessedOnly && !in_array($fieldName, $includeFields, true) && $this->context->getFieldTracker()->hasBeenProcessed($fieldName)) {
                continue;
            }

            $this->addField($target, (string)$fieldName, $value);
        }

        return $target;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();

        $enabledSchema = new BooleanSchema(static::DEFAULT_ENABLED);
        $enabledSchema->getRenderingDefinition()->setLabel('Pass through Fields');
        $schema->addProperty(static::KEY_ENABLED, $enabledSchema);

        $unprocessedOnlySchema = new BooleanSchema(static::DEFAULT_UNPROCESSED_ONLY);
        $unprocessedOnlySchema->getRenderingDefinition()->setLabel('Pass through only unprocessed fields');
        $schema->addProperty(static::KEY_UNPROCESSED_ONLY, $unprocessedOnlySchema);

        $includeFieldsSchema = new StringSchema(static::DEFAULT_INCLUDE_FIELDS);
        $schema->addProperty(static::KEY_INCLUDE_FIELDS, $includeFieldsSchema);

        return $schema;
    }
}
