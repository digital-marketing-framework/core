<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class InComparison extends BinaryComparison
{
    public static function getLabel(): ?string
    {
        return 'is in';
    }

    protected function compareValues(string|ValueInterface|null $a, string|ValueInterface|null $b): bool
    {
        return GeneralUtility::isInList($a, GeneralUtility::castValueToArray($b));
    }
}
