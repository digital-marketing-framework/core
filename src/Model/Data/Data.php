<?php

namespace DigitalMarketingFramework\Core\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class Data extends MultiValue implements DataInterface
{
    public function fieldExists(string $key): bool
    {
        return array_key_exists($key, iterator_to_array($this));
    }

    public function fieldEmpty(string $key): bool
    {
        return !$this->fieldExists($key) || GeneralUtility::isEmpty($this[$key]);
    }

    public function getFieldNames(): array
    {
        /** @var array<string> */
        return array_keys($this->toArray());
    }

    public static function unpack(array $packed): DataInterface
    {
        /** @var Data */
        $unpacked = parent::unpack($packed);

        return $unpacked;
    }
}
