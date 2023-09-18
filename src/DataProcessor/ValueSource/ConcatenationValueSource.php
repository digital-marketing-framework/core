<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class ConcatenationValueSource extends ValueSource
{
    public const WEIGHT = 3;

    public const KEY_GLUE = 'glue';

    public const DEFAULT_GLUE = '\\s';

    public const KEY_VALUES = 'values';

    public const DEFAULT_VALUES = [];

    public function build(): null|string|ValueInterface
    {
        $glue = GeneralUtility::parseSeparatorString($this->getConfig(static::KEY_GLUE));

        $values = [];
        foreach ($this->getListConfig(static::KEY_VALUES) as $valueConfig) {
            $value = $this->dataProcessor->processValue($valueConfig, $this->context->copy());
            if ($value !== null) {
                $values[] = $value;
            }
        }

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
        $schema->addProperty(static::KEY_VALUES, new ListSchema(new CustomSchema(ValueSchema::TYPE)));

        return $schema;
    }

    public static function canBeMultiValue(): bool
    {
        return false;
    }
}
