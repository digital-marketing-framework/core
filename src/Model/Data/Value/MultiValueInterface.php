<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

use ArrayAccess;
use IteratorAggregate;

/**
 * @extends ArrayAccess<int|string,string|ValueInterface>
 * @extends IteratorAggregate<int|string,string|ValueInterface>
 */
interface MultiValueInterface extends ValueInterface, ArrayAccess, IteratorAggregate
{
    /**
     * @param array<int|string,string|ValueInterface> $a
     */
    public function __construct(array $a = []);

    /**
     * @return array<int|string,string|ValueInterface>
     */
    public function toArray(): array;

    public function count(): int;

    public function empty(): bool;

    public function setGlue(string $glue): void;

    public function getGlue(): string;

    public function __toString(): string;

    public function pack(): array;

    public static function unpack(array $packed): MultiValueInterface;
}
