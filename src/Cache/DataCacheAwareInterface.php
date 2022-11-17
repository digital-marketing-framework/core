<?php

namespace DigitalMarketingFramework\Core\Cache;

interface DataCacheAwareInterface
{
    public function setCache(DataCacheInterface $cache): void;
}
