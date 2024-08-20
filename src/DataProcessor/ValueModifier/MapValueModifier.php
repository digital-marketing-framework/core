<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class MapValueModifier extends ValueModifier
{
    public const WEIGHT = 50;

    public const KEY_MAP = 'map';

    protected function modifyValue(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value === null) {
            return null;
        }

        return $this->getMapConfig(static::KEY_MAP)[(string)$value] ?? $value;
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_MAP, new MapSchema(new StringSchema('mappedValue'), new StringSchema('value')));

        return $schema;
    }
}
