<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

class AndEvaluation extends Evaluation
{
    public const WEIGHT = 4;

    public function evaluate(): bool
    {
        $result = true;
        foreach ($this->configuration as $subEvaluationConfig) {
            if (!$this->dataProcessor->processEvaluation($subEvaluationConfig, $this->context->copy())) {
                $result = false;
            }
        }
        return $result;
    }
}
