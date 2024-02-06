<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class TrimValueModifier extends ValueModifier
{
    public const WEIGHT = 1;

    protected function modifyValue(string|ValueInterface|null $value): string|ValueInterface|null
    {
        return $value !== null ? trim((string)$value) : null;
    }
}
