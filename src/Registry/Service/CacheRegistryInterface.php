<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Cache\DataCacheInterface;

interface CacheRegistryInterface
{
    public function getCache(): DataCacheInterface;

    public function setCache(DataCacheInterface $cache): void;
}
