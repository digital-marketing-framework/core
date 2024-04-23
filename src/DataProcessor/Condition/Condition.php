<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

abstract class Condition extends DataProcessorPlugin implements ConditionInterface
{
    abstract public function evaluate(): bool;

    public static function getSchema(): SchemaInterface
    {
        return new ContainerSchema();
    }
}
