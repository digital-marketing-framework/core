<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

class OrEvaluation extends AbstractClauseEvaluation
{
    protected function initialValue(): bool
    {
        return false;
    }

    protected function calculate(bool $result, EvaluationInterface $evaluation): bool
    {
        return $evaluation->eval() || $result;
    }
}
