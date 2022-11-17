<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

class ExistsEvaluation extends Evaluation
{
    public function eval(): bool
    {
        // exists
        $key = $this->getKeyFromContext();
        $result = $key && $this->fieldExists($key);

        // does not exist
        if (!$this->configuration) {
            $result = !$result;
        }

        return $result;
    }
}
