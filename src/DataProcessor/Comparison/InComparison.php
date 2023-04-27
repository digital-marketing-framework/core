<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class InComparison extends BinaryComparison
{
    protected function compareValues(string|null|ValueInterface $a, string|null|ValueInterface $b): bool
    {
        return GeneralUtility::isInList($a, GeneralUtility::castValueToArray($b));
    }
}
