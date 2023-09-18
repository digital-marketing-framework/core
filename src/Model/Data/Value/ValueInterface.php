<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

use Stringable;

interface ValueInterface extends Stringable
{
    public function __toString(): string;

    public function getValue(): mixed;

    /**
     * @return array<string,mixed>
     */
    public function pack(): array;

    /**
     * @param array<string,mixed> $packed
     */
    public static function unpack(array $packed): ValueInterface;
}
