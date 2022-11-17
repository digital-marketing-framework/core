<?php

namespace DigitalMarketingFramework\Core\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;

interface DataInterface extends MultiValueInterface
{
    public function toArray(): array;

    public function fieldExists($key): bool;
    public function fieldEmpty($key): bool;

    public function pack(): array;
    public static function unpack(array $packed): DataInterface;
}
