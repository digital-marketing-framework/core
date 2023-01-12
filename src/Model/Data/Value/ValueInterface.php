<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

interface ValueInterface
{
    public function __toString(): string;
    public function getValue(): mixed;

    public function pack(): array;
    public static function unpack(array $packed): ValueInterface;
}
