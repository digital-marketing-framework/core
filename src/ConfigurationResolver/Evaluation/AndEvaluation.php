<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

class AndEvaluation extends AbstractClauseEvaluation
{
    protected function initialValue(): bool
    {
        return true;
    }

    protected function calculate(bool $result, EvaluationInterface $evaluation): bool
    {
        return $evaluation->eval() && $result;
    }
}
