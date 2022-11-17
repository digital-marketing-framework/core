<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

class ProcessedEvaluation extends Evaluation
{
    public function eval(): bool
    {
        // processed
        $key = $this->getKeyFromContext();
        $result = $key && $this->context->getFieldTracker()->hasBeenProcessed($key);

        // not processed
        if (!$this->configuration) {
            $result = !$result;
        }

        return $result;
    }
}
