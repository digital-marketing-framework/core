<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

use ArrayAccess;
use IteratorAggregate;

interface MultiValueInterface extends ValueInterface, ArrayAccess, IteratorAggregate
{
    public function __construct(array $a = []);
    public function toArray(): array;
    public function count(): int;
    public function empty(): bool;
    public function setGlue(string $glue): void;
    public function getGlue(): string;
    public function __toString(): string;
    public function pack(): array;
    public static function unpack(array $packed): MultiValueInterface;
}
