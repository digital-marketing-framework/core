<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class MapValueModifier extends ValueModifier
{
    public const WEIGHT = 50;

    public const KEY_MAP = 'map';
    public const DEFAULT_MAP = [];

    protected function modifyValue(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if ($value === null) {
            return null;
        }
        return $this->getConfig(static::KEY_MAP)[(string) $value] ?? $value;
    }
}
