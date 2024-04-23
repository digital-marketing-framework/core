<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\IntegerValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\ValueSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class IntegerValueSource extends ValueSource
{
    public const KEY_VALUE = 'value';

    public const DEFAULT_VALUE = null;

    public function build(): string|ValueInterface|null
    {
        $value = $this->getConfig(static::KEY_VALUE);
        if ($value !== null) {
            $value = $this->dataProcessor->processValue($value, $this->context->copy());
        }

        if ($value === null) {
            return null;
        }

        $value = (string)$value;
        if (!is_numeric($value)) {
            return null;
        }

        return new IntegerValue((int)$value);
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_VALUE, new CustomSchema(ValueSchema::TYPE));

        return $schema;
    }

    public static function canBeMultiValue(): bool
    {
        return false;
    }
}
