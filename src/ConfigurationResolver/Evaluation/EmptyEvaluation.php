<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class EmptyEvaluation extends AbstractIsEvaluation
{
    protected function evalValue($fieldValue)
    {
        return GeneralUtility::isEmpty($fieldValue);
    }
}
