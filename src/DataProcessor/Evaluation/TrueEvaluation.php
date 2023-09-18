<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Evaluation;

class TrueEvaluation extends Evaluation
{
    public const WEIGHT = 0;

    public function evaluate(): bool
    {
        return true;
    }
}
