<?php

namespace DigitalMarketingFramework\Core\Cache;

interface CacheAwareInterface
{
    public function setCache(CacheInterface $cache): void;
}
