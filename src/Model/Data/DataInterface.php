<?php

namespace DigitalMarketingFramework\Core\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;

interface DataInterface extends MultiValueInterface
{
    public function toArray(): array;

    public function fieldExists(string $key): bool;

    public function fieldEmpty(string $key): bool;

    /**
     * @return array<string>
     */
    public function getFieldNames(): array;

    public function pack(): array;

    public static function unpack(array $packed): DataInterface;
}
