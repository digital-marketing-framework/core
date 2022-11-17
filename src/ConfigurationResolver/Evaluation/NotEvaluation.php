<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

class NotEvaluation extends Evaluation
{
    public function eval(): bool
    {
        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword('general', $this->configuration);
        return !$evaluation->eval();
    }
}
