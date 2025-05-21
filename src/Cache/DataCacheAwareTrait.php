<?php

namespace DigitalMarketingFramework\Core\Cache;

/** @phpstan-ignore-next-line This trait can be used by other packages, even though it is not used in this one. */
trait DataCacheAwareTrait
{
    protected DataCacheInterface $cache;

    public function setCache(DataCacheInterface $cache): void
    {
        $this->cache = $cache;
    }
}
