<?php

namespace DigitalMarketingFramework\Core\Utility;

use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;

final class CacheUtility
{
    /**
     * @param array<DataInterface> $dataList
     */
    public static function mergeData(array $dataList, bool $override = false): DataInterface
    {
        $result = new Data();
        foreach ($dataList as $data) {
            foreach ($data as $key => $value) {
                if ($override || $result->fieldEmpty((string)$key)) {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }
}
