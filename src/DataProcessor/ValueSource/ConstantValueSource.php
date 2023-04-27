<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class ConstantValueSource extends ValueSource
{
    public const WEIGHT = 1;

    public const KEY_VALUE = 'value';
    public const DEFAULT_VALUE = '';

    public function build(): null|string|ValueInterface
    {
        return $this->getConfig(static::KEY_VALUE);
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_VALUE => static::DEFAULT_VALUE,
        ];
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
