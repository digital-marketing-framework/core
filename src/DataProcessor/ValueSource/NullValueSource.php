<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class NullValueSource extends ValueSource
{
    public const WEIGHT = 30;

    public function build(): null|string|ValueInterface
    {
        return null;
    }

    public static function modifiable(): bool
    {
        return false;
    }

    public static function canBeMultiValue(): bool
    {
        return false;
    }
}
