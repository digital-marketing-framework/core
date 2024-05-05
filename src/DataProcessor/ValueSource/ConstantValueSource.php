<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class ConstantValueSource extends ValueSource
{
    public const WEIGHT = 1;

    public const KEY_VALUE = 'value';

    public const DEFAULT_VALUE = '';

    public function build(): string|ValueInterface|null
    {
        return $this->getConfig(static::KEY_VALUE);
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_VALUE, new StringSchema(static::DEFAULT_VALUE));

        return $schema;
    }
}
