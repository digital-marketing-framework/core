<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

interface IntegerValueInterface extends ValueInterface
{
    public function getValue(): int;

    public function setValue(mixed $value): void;
}
