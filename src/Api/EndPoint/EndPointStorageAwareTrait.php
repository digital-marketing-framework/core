<?php

namespace DigitalMarketingFramework\Core\Api\EndPoint;

trait EndPointStorageAwareTrait
{
    protected EndPointStorageInterface $endPointStorage;

    public function setEndPointStorage(EndPointStorageInterface $endPointStorage): void
    {
        $this->endPointStorage = $endPointStorage;
    }
}
