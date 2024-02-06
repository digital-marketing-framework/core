<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class LowerCaseValueModifier extends ValueModifier
{
    public const WEIGHT = 3;

    protected function modifyValue(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value === null) {
            return null;
        }

        return strtolower((string)$value);
    }
}
