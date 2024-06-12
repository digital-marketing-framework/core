<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class ExistsComparison extends UnaryComparison
{
    public static function getLabel(): ?string
    {
        return 'exists';
    }

    public static function handleMultiValuesIndividually(): bool
    {
        return false;
    }

    protected function compareValue(string|ValueInterface|null $value): bool
    {
        return $value !== null;
    }
}
