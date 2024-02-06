<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IsFalseComparison extends UnaryComparison
{
    protected function compareValue(string|ValueInterface|null $value): bool
    {
        return GeneralUtility::isFalse($value);
    }
}
