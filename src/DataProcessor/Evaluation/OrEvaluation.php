<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

class OrEvaluation extends Evaluation
{
    public const WEIGHT = 5;

    public function evaluate(): bool
    {
        if (empty($this->configuration)) {
            return true;
        }
        $result = false;
        foreach ($this->configuration as $subEvaluationConfig) {
            if ($this->dataProcessor->processEvaluation($subEvaluationConfig, $this->context->copy())) {
                $result = true;
            }
        }
        return $result;
    }
}
