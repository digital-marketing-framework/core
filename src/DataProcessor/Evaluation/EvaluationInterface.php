<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPluginInterface;

interface EvaluationInterface extends DataProcessorPluginInterface
{
    public function evaluate(): bool;

    public static function getSchema(): SchemaInterface;
}
