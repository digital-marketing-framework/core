<?php

namespace DigitalMarketingFramework\Core\Api\EndPoint;

interface EndPointStorageAwareInterface
{
    public function setEndPointStorage(EndPointStorageInterface $storage): void;
}
