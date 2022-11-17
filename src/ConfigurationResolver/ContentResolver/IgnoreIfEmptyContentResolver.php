<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IgnoreIfEmptyContentResolver extends IgnoreContentResolver
{
    protected const WEIGHT = 102;

    protected function ignore($result): bool
    {
        return parent::ignore($result) && GeneralUtility::isEmpty($result);
    }
}
