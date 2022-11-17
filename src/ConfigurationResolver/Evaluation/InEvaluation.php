<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationBehaviour;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class InEvaluation extends Evaluation
{
    protected function getConfigurationBehaviour(): ConfigurationBehaviour
    {
        return ConfigurationBehaviour::ResolveContentThenCastToArray;
    }

    public function eval(): bool
    {
        return GeneralUtility::isInList($this->getSelectedValue(), $this->configuration);
    }
}
