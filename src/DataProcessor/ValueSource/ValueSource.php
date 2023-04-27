<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

abstract class ValueSource extends DataProcessorPlugin implements ValueSourceInterface
{
    abstract public function build(): null|string|ValueInterface;

    public static function getSchema(): SchemaInterface
    {
        return new ContainerSchema();
    }

    public static function modifiable(): bool
    {
        return true;
    }

    public static function canBeMultiValue(): bool
    {
        return true;
    }
}
