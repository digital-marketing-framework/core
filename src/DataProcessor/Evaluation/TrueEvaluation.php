<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class TrueEvaluation extends Evaluation
{
    public const WEIGHT = 0;

    public function evaluate(): bool
    {
        return true;
    }
}
