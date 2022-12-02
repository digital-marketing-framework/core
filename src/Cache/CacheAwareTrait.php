<?php

namespace DigitalMarketingFramework\Core\Cache;

trait CacheAwareTrait
{
    protected CacheInterface $cache;

    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }
}
