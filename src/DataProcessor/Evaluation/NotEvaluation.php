<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

class NotEvaluation extends Evaluation
{
    public const WEIGHT = 3;

    public function evaluate(): bool
    {
        return !$this->dataProcessor->processEvaluation($this->configuration, $this->context->copy());
    }
}
