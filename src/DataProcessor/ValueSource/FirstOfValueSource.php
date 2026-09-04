<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class FirstOfValueSource extends ValueSource
{
    public const WEIGHT = 6;

    public const KEY_SKIP_EMPTY_VALUES = 'skipEmptyValues';

    public const DEFAULT_SKIP_EMPTY_VALUES = false;

    public const KEY_VALUE_LIST = 'listValues';

    public function build(): string|ValueInterface|null
    {
        $skipEmptyValues = $this->getBoolConfig(static::KEY_SKIP_EMPTY_VALUES);

        $valueList = $this->getListConfig(static::KEY_VALUE_LIST);
        foreach ($valueList as $valueConfig) {
            $value = $this->dataProcessor->processValue($valueConfig, $this->context->copy());

            // null values are always skipped
            if ($value === null) {
                continue;
            }

            if ($skipEmptyValues && GeneralUtility::isEmpty($value)) {
                continue;
            }

            return $value;
        }

        return null;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $skipEmptyValuesSchema = new BooleanSchema(static::DEFAULT_SKIP_EMPTY_VALUES);
        $skipEmptyValuesSchema->getRenderingDefinition()->setHint('By default, a value counts as found as soon as its field exists, even when that field is empty. Enable this to treat empty fields as not found as well, so that the search continues with the next entry in the list.');
        $schema->addProperty(static::KEY_SKIP_EMPTY_VALUES, $skipEmptyValuesSchema);
        $schema->addProperty(static::KEY_VALUE_LIST, new ListSchema(new CustomSchema(ValueSchema::TYPE)));

        return $schema;
    }
}
