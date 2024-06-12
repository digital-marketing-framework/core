<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IsTrueComparison extends UnaryComparison
{
    public static function getLabel(): ?string
    {
        return 'is true';
    }

    protected function compareValue(string|ValueInterface|null $value): bool
    {
        return GeneralUtility::isTrue($value);
    }
}
