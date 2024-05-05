<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Condition;

class FalseCondition extends Condition
{
    public const WEIGHT = 1;

    public function evaluate(): bool
    {
        return false;
    }
}
