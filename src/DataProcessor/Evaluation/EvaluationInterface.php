<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPluginInterface;

interface EvaluationInterface extends DataProcessorPluginInterface
{
    public function evaluate(): bool;
}
