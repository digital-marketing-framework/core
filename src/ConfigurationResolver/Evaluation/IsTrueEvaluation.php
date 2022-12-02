<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IsTrueEvaluation extends AbstractIsEvaluation
{
    protected function evalValue($fieldValue): bool
    {
        return GeneralUtility::isTrue($fieldValue);
    }
}
