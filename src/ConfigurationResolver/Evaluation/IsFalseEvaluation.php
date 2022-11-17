<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IsFalseEvaluation extends AbstractIsEvaluation
{
    protected function evalValue($fieldValue)
    {
        return GeneralUtility::isFalse($fieldValue);
    }
}
