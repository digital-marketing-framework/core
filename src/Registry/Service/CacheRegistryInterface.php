<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Cache\CacheInterface;

interface CacheRegistryInterface
{
    public function getCache(): CacheInterface;
    public function setCache(CacheInterface $cache): void;
}
