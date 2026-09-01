<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class ConcatenationValueSource extends ValueSource
{
    public const WEIGHT = 3;

    public const KEY_GLUE = 'glue';

    public const DEFAULT_GLUE = '\\s';

    public const KEY_SKIP_EMPTY_VALUES = 'skipEmptyValues';

    public const DEFAULT_SKIP_EMPTY_VALUES = false;

    public const KEY_VALUES = 'values';

    public function build(): string|ValueInterface|null
    {
        $glue = GeneralUtility::parseSeparatorString($this->getConfig(static::KEY_GLUE));
        $skipEmptyValues = $this->getBoolConfig(static::KEY_SKIP_EMPTY_VALUES);

        $values = [];
        foreach ($this->getListConfig(static::KEY_VALUES) as $valueConfig) {
            $value = $this->dataProcessor->processValue($valueConfig, $this->context->copy());

            // null values are always skipped
            if ($value === null) {
                continue;
            }

            if ($skipEmptyValues && GeneralUtility::isEmpty($value)) {
                continue;
            }

            $values[] = $value;
        }

        // values that have been skipped do not count as found,
        // so concatenating nothing but empty values still results in null
        if ($values === []) {
            return null;
        }

        if (count($values) === 1) {
            return reset($values);
        }

        return implode($glue, $values);
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_GLUE, new StringSchema(static::DEFAULT_GLUE));
        $schema->addProperty(static::KEY_SKIP_EMPTY_VALUES, new BooleanSchema(static::DEFAULT_SKIP_EMPTY_VALUES));
        $schema->addProperty(static::KEY_VALUES, new ListSchema(new CustomSchema(ValueSchema::TYPE)));

        return $schema;
    }

    public static function canBeMultiValue(): bool
    {
        return false;
    }
}
