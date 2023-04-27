<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class EqualsComparison extends BinaryComparison
{
    protected function compareValues(string|null|ValueInterface $a, string|null|ValueInterface $b): bool
    {
        return GeneralUtility::compare($a, $b);
    }
}
