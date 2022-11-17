<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationBehaviour;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class RequiredEvaluation extends Evaluation
{
    protected function getConfigurationBehaviour(): ConfigurationBehaviour
    {
        return ConfigurationBehaviour::ResolveContentThenCastToArray;
    }

    public function eval(): bool
    {
        $result = true;
        foreach ($this->configuration as $requiredField) {
            if (GeneralUtility::isEmpty($this->getFieldValue($requiredField))) {
                $result = false;
                break;
            }
        }
        return $result;
    }
}
