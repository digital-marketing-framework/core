<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorPlugin;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

abstract class ValueSource extends DataProcessorPlugin implements ValueSourceInterface
{
    abstract public function build(): null|string|ValueInterface;

    public static function modifiable(): bool
    {
        return true;
    }

    public static function canBeMultiValue(): bool
    {
        return true;
    }
}
