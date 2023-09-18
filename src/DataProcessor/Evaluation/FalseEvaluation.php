<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

class FalseEvaluation extends Evaluation
{
    public const WEIGHT = 1;

    public function evaluate(): bool
    {
        return false;
    }
}
