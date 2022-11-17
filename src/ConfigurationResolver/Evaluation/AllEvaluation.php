<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

class AllEvaluation extends AnyEvaluation
{
    protected function initialValue(): bool
    {
        return true;
    }

    protected function calculateResult(bool $indexResult, bool $overallResult)
    {
        return $indexResult && $overallResult;
    }
}
