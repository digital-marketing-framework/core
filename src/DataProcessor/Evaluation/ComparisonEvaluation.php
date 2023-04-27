<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

class ComparisonEvaluation extends Evaluation
{
    public const WEIGHT = 2;

    public function evaluate(): bool
    {
        return $this->dataProcessor->processComparison($this->configuration, $this->context->copy());
    }
}
