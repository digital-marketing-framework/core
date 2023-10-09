<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class UpperCaseValueModifier extends ValueModifier
{
    public const WEIGHT = 2;

    protected function modifyValue(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if ($value === null) {
            return null;
        }

        return strtoupper((string)$value);
    }
}
