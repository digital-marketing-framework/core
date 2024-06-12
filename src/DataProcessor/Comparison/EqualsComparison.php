<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class EqualsComparison extends BinaryComparison
{
    public static function getLabel(): ?string
    {
        return 'equals';
    }

    protected function compareValues(string|ValueInterface|null $a, string|ValueInterface|null $b): bool
    {
        return GeneralUtility::compare($a, $b);
    }
}
