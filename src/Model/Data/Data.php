<?php

namespace DigitalMarketingFramework\Core\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class Data extends MultiValue implements DataInterface
{
    public function fieldExists($key): bool
    {
        return array_key_exists($key, iterator_to_array($this));
    }

    public function fieldEmpty($key): bool
    {
        return !$this->fieldExists($key) || GeneralUtility::isEmpty($this[$key]);
    }

    public static function unpack(array $packed): DataInterface
    {
        /** @var Data */
        $unpacked = parent::unpack($packed);
        return $unpacked;
    }
}
