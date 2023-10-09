<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

abstract class Value implements ValueInterface
{
    public function getValue(): mixed
    {
        return (string)$this;
    }
}
