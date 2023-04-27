<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class ExistsComparison extends UnaryComparison
{
    public static function handleMultiValuesIndividually(): bool
    {
        return false;
    }

    protected function compareValue(string|null|ValueInterface $value): bool
    {
        return $value !== null;
    }
}
