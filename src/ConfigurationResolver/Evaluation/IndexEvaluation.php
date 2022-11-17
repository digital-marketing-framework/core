<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

class IndexEvaluation extends Evaluation
{
    public function eval(): bool
    {
        if (!is_array($this->configuration) || count($this->configuration) !== 1) {
                return false;
        }

        $key = array_keys($this->configuration)[0];
        $this->addIndexToContext($key);
        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword('general', $this->configuration[$key]);
        return $evaluation->eval();
    }
}
