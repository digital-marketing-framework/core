<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;

abstract class Evaluation extends DataProcessorPlugin implements EvaluationInterface
{
    abstract public function evaluate(): bool;

    public static function getSchema(): SchemaInterface
    {
        return new ContainerSchema();
    }
}
