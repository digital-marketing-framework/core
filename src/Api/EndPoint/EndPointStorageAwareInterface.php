<?php

namespace DigitalMarketingFramework\Core\Api\EndPoint;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

interface EndPointStorageAwareInterface
{
    /**
     * @param EndPointStorageInterface<EndPointInterface> $storage
     */
    public function setEndPointStorage(EndPointStorageInterface $storage): void;
}
