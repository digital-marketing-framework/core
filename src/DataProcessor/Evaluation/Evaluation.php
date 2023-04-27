<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;

abstract class Evaluation extends DataProcessorPlugin implements EvaluationInterface
{
    abstract public function evaluate(): bool;
}
