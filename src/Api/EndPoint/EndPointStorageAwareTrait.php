<?php

namespace DigitalMarketingFramework\Core\Api\EndPoint;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

trait EndPointStorageAwareTrait
{
    /** @var EndPointStorageInterface<EndPointInterface> */
    protected EndPointStorageInterface $endPointStorage;

    /**
     * @param EndPointStorageInterface<EndPointInterface> $endPointStorage
     */
    public function setEndPointStorage(EndPointStorageInterface $endPointStorage): void
    {
        $this->endPointStorage = $endPointStorage;
    }
}
