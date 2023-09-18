<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Cache\DataCache;
use DigitalMarketingFramework\Core\Cache\DataCacheInterface;
use DigitalMarketingFramework\Core\Cache\NullCache;

trait CacheRegistryTrait
{
    protected DataCacheInterface $cache;

    public function getCache(): DataCacheInterface
    {
        if (!isset($this->cache)) {
            $this->cache = new DataCache(new NullCache());
        }

        return $this->cache;
    }

    public function setCache(DataCacheInterface $cache): void
    {
        $this->cache = $cache;
    }
}
