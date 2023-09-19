<?php

namespace DigitalMarketingFramework\Core\Cache;

class TaggableCacheWrapper implements CacheInterface
{
    protected const KEY_DATA = 'data';

    protected const KEY_TAGS = 'tags';

    protected const PREFIX_KEY = 'key:';

    protected const PREFIX_TAG = 'tag:';

    protected const KEY_ALL_TAGS = 'TAGABLE_WRAPPPER__ALL_TAGS';

    public function __construct(
        protected NonTaggableCacheInterface $cache,
    ) {
    }

    public function setTimeoutInSeconds(int $timeout): void
    {
        $this->cache->setTimeoutInSeconds($timeout);
    }

    public function getTimeoutInSeconds(): int
    {
        return $this->cache->getTimeoutInSeconds();
    }

    /**
     * @return ?array<mixed>
     */
    public function fetch(string $key): ?array
    {
        return $this->cache->fetch(static::PREFIX_KEY . $key)[static::KEY_DATA] ?? null;
    }

    /**
     * @param array<string> $keys
     *
     * @return array<array<mixed>>
     */
    public function fetchMultiple(array $keys): array
    {
        return array_map(static function ($result) {
            return $result[static::KEY_DATA];
        }, $this->cache->fetchMultiple($keys));
    }

    public function purge(string $key): void
    {
        $data = $this->cache->fetch(static::PREFIX_KEY . $key);
        if ($data !== null) {
            foreach ($data[static::KEY_TAGS] as $tag) {
                $tagData = $this->cache->fetch(static::PREFIX_TAG . $tag);
                if ($tagData !== null) {
                    $tagData = array_filter($tagData, static function ($tagKey) use ($key) {
                        return $tagKey !== $key;
                    });
                    if ($tagData !== []) {
                        $this->cache->store(static::PREFIX_TAG . $tag, $tagData);
                    } else {
                        $this->cache->purge(static::PREFIX_TAG . $tag);
                    }
                }
            }
        }

        $this->cache->purge(static::PREFIX_KEY . $key);
    }

    public function purgeMultiple(array $keys): void
    {
        foreach ($keys as $key) {
            $this->purge($key);
        }
    }

    public function purgeExpired(): void
    {
        $this->cache->purgeExpired();
        $allTags = $this->cache->fetch(static::KEY_ALL_TAGS);
        if ($allTags !== null) {
            foreach ($allTags as $tag) {
                $tagData = $this->cache->fetch(static::PREFIX_TAG . $tag);
                if ($tagData !== null) {
                    $newTagData = [];
                    foreach ($tagData as $tagKey) {
                        if ($this->cache->fetch(static::PREFIX_KEY . $tagKey) !== null) {
                            $newTagData[] = $tagKey;
                        }
                    }

                    if ($newTagData === []) {
                        $this->cache->purge(static::PREFIX_TAG . $tag);
                    } else {
                        $this->cache->store(static::PREFIX_TAG . $tag, $newTagData);
                    }
                }
            }
        }
    }

    public function purgeAll(): void
    {
        $this->cache->purgeAll();
    }

    /**
     * @param array<string> $tags
     *
     * @return array<string>
     */
    protected function fetchKeysByTags(array $tags): array
    {
        $tagDataList = $this->cache->fetchMultiple(array_map(static function ($tag) {
            return static::PREFIX_TAG . $tag;
        }, $tags));
        $keys = [];
        foreach ($tagDataList as $tagData) {
            array_push($keys, ...$tagData);
        }

        return array_unique($keys);
    }

    /**
     * @param array<mixed> $data
     * @param array<string> $tags
     */
    public function store(string $key, array $data, array $tags = []): void
    {
        $this->purge($key);
        $this->cache->store(static::PREFIX_KEY . $key, [static::KEY_DATA => $data, static::KEY_TAGS => $tags]);
        foreach ($tags as $tag) {
            $tagData = $this->cache->fetch(static::PREFIX_TAG . $tag);
            if ($tagData === null) {
                $tagData = [$key];
            } elseif (!in_array($key, $tagData)) {
                $tagData[] = $key;
            }

            $this->cache->store(static::PREFIX_TAG . $tag, $tagData);
        }
    }

    /**
     * @param array<string> $tags
     */
    public function purgeByTags(array $tags): void
    {
        $keys = $this->fetchKeysByTags($tags);
        $this->purgeMultiple($keys);
    }
}
