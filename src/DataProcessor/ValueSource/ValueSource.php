<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

abstract class ValueSource extends DataProcessorPlugin implements ValueSourceInterface
{
    abstract public function build(): string|ValueInterface|null;

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
