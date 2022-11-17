<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class EqualsEvaluation extends Evaluation
{
    public function eval(): bool
    {
        return GeneralUtility::compare(
            $this->getSelectedValue(),
            $this->resolveContent($this->configuration)
        );
    }
}
