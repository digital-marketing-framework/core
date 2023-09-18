<?php

namespace DigitalMarketingFramework\Core\Cache;

interface CacheInterface extends NonTaggableCacheInterface
{
    /**
     * @param array<mixed> $data
     * @param array<string> $tags
     */
    public function store(string $key, array $data, array $tags = []): void;

    /**
     * @param array<string> $tags
     */
    public function purgeByTags(array $tags): void;
}
