<?php

namespace DigitalMarketingFramework\Core\Cache;

trait DataCacheAwareTrait
{
    protected DataCacheInterface $cache;

    public function setCache(DataCacheInterface $cache): void
    {
        $this->cache = $cache;
    }
}
