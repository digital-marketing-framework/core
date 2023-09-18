<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class SprintfValueModifier extends ValueModifier
{
    public const KEY_FORMAT = 'format';

    public const DEFAULT_FORMAT = '%s';

    public function modify(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if (!$this->proceed()) {
            return $value;
        }

        if ($value === null) {
            return null;
        }

        $format = $this->getConfig(static::KEY_FORMAT);
        $values = $value instanceof MultiValueInterface ? $value->toArray() : [$value];

        return sprintf($format, ...$values);
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_FORMAT, new StringSchema(static::DEFAULT_FORMAT));

        return $schema;
    }
}
