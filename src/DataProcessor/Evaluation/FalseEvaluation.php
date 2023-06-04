<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class FalseEvaluation extends Evaluation
{
    public const WEIGHT = 1;

    public function evaluate(): bool
    {
        return false;
    }
}
