<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IsTrueComparison extends UnaryComparison
{
    protected function compareAnyEmpty(): bool
    {
        return false;
    }

    protected function compareAllEmpty(): bool
    {
        return false;
    }

    protected function compareValue(string|null|ValueInterface $value): bool
    {
        return GeneralUtility::isTrue($value);
    }
}
