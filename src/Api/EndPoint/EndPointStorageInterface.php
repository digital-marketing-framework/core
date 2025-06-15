<?php

namespace DigitalMarketingFramework\Core\Api\EndPoint;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Storage\ItemStorageInterface;

/**
 * @extends ItemStorageInterface<EndPointInterface>
 */
interface EndPointStorageInterface extends ItemStorageInterface
{
    public function fetchByName(string $name): ?EndPointInterface;
}
