<?php

namespace DigitalMarketingFramework\Core\Tests;

use DigitalMarketingFramework\Core\Utility\ListUtility;
use DigitalMarketingFramework\Core\Utility\MapUtility;

trait ListMapTestTrait
{
    /**
     * @return array{uuid:string,weight:int,value:mixed}
     */
    protected static function createListItem(mixed $value, string $id, int $weight = 10): array
    {
        return [
            ListUtility::KEY_UID => $id,
            ListUtility::KEY_WEIGHT => $weight,
            ListUtility::KEY_VALUE => $value,
        ];
    }

    /**
     * @return array{uuid:string,weight:int,key:string,value:mixed}
     */
    protected static function createMapItem(string $key, mixed $value, string $id, int $weight = 10): array
    {
        return [
            MapUtility::KEY_UID => $id,
            MapUtility::KEY_WEIGHT => $weight,
            MapUtility::KEY_KEY => $key,
            MapUtility::KEY_VALUE => $value,
        ];
    }
}
