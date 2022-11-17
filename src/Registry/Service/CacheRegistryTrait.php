<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Cache\CacheInterface;

trait CacheRegistryTrait
{
    protected CacheInterface $cache;

    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }
}
