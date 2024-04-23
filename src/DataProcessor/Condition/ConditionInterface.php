<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPluginInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

interface ConditionInterface extends DataProcessorPluginInterface
{
    public function evaluate(): bool;

    public static function getSchema(): SchemaInterface;
}
