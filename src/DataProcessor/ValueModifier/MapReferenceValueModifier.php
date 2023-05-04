<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class MapReferenceValueModifier extends ValueModifier
{
    public const WEIGHT = 40;

    public const KEY_MAP_NAME = 'reference';
    public const DEFAULT_MAP_NAME = '';

    public const KEY_INVERT = 'invert';
    public const DEFAULT_INVERT = false;

    protected function modifyValue(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if ($value === null) {
            return null;
        }
        $map = $this->context->getConfiguration()->getValueMapConfiguration($this->getConfig(static::KEY_MAP_NAME));
        if ($map !== null) {
            if ($this->getConfig(static::KEY_INVERT)) {
                $map = array_flip($map);
            }
            return $map[(string)$value] ?? $value;
        }
        return $value;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_MAP_NAME => static::DEFAULT_MAP_NAME,
            static::KEY_INVERT => static::DEFAULT_INVERT,
        ];
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_MAP_NAME, new StringSchema());
        $schema->addProperty(static::KEY_INVERT, new BooleanSchema(false));
        return $schema;
    }
}
