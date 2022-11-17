<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IsTrueEvaluation extends AbstractIsEvaluation
{
    protected function evalValue($fieldValue)
    {
        return GeneralUtility::isTrue($fieldValue);
    }
}
