<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

abstract class AbstractIsEvaluation extends Evaluation
{
    public function eval(): bool
    {
        // positive evaluation
        $result = parent::eval();

        // negative evaluation
        if (!$this->configuration) {
            $result = !$result;
        }

        return $result;
    }
}
