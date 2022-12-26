<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Cache\DataCacheInterface;

trait CacheRegistryTrait
{
    protected DataCacheInterface $cache;

    public function getCache(): DataCacheInterface
    {
        return $this->cache;
    }

    public function setCache(DataCacheInterface $cache): void
    {
        $this->cache = $cache;
    }
}
