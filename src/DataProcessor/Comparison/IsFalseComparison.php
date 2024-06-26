<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IsFalseComparison extends UnaryComparison
{
    public static function getLabel(): ?string
    {
        return 'is false';
    }

    protected function compareAnyEmpty(bool $secondOperandEmpty = true): bool
    {
        return true;
    }

    protected function compareAllEmpty(bool $secondOperandEmpty = true): bool
    {
        return true;
    }

    protected function compareValue(string|ValueInterface|null $value): bool
    {
        return GeneralUtility::isFalse($value);
    }
}
