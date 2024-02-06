<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IsEmptyComparison extends UnaryComparison
{
    public static function handleMultiValuesIndividually(): bool
    {
        return false;
    }

    protected function compareValue(string|ValueInterface|null $value): bool
    {
        return GeneralUtility::isEmpty($value);
    }
}
