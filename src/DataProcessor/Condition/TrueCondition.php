<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Condition;

class TrueCondition extends Condition
{
    public const WEIGHT = 0;

    public function evaluate(): bool
    {
        return true;
    }
}
