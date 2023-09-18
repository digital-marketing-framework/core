<?php

namespace DigitalMarketingFramework\Core\Cache;

use DigitalMarketingFramework\Core\Model\Cache\CacheEntry;
use DigitalMarketingFramework\Core\Model\Cache\CacheEntryInterface;

class NonPersistentCache implements CacheInterface
{
    /** @var array<string,CacheEntryInterface> */
    protected array $storage = [];

    protected int $timeoutInSeconds = 86400;

    public function setTimeoutInSeconds(int $timeout): void
    {
        $this->timeoutInSeconds = $timeout;
    }

    public function getTimeoutInSeconds(): int
    {
        return $this->timeoutInSeconds;
    }

    public function fetch(string $key): ?array
    {
        $entry = $this->storage[$key] ?? null;
        if ($entry instanceof CacheEntryInterface && $entry->expired($this->timeoutInSeconds)) {
            unset($this->storage[$key]);
            $entry = null;
        }

        return $entry?->getData();
    }

    /**
     * @param array<string> $keys
     *
     * @return array<array<mixed>>
     */
    public function fetchMultiple(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $data = $this->fetch($key);
            if ($data !== null) {
                $result[] = $data;
            }
        }

        return $result;
    }

    /**
     * @param array<string> $tags
     *
     * @return array<CacheEntryInterface>
     */
    protected function fetchEntriesByTags(array $tags): array
    {
        $result = [];
        foreach ($this->storage as $key => $entry) {
            if ($entry->expired($this->timeoutInSeconds)) {
                unset($this->storage[$key]);
                continue;
            }

            foreach ($entry->getTags() as $tag) {
                if (in_array($tag, $tags)) {
                    $result[] = $entry;
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * @param array<string> $tags
     */
    public function store(string $key, array $data, array $tags = []): void
    {
        $this->storage[$key] = new CacheEntry($key, $data, $tags);
    }

    public function purge(string $key): void
    {
        unset($this->storage[$key]);
    }

    /**
     * @param array<string> $keys
     */
    public function purgeMultiple(array $keys): void
    {
        foreach ($keys as $key) {
            $this->purge($key);
        }
    }

    /**
     * @param array<string> $tags
     */
    public function purgeByTags(array $tags): void
    {
        $entries = $this->fetchEntriesByTags($tags);
        foreach ($entries as $entry) {
            $this->purge($entry->getKey());
        }
    }

    public function purgeExpired(): void
    {
        foreach ($this->storage as $identifier => $entry) {
            if ($entry->expired($this->timeoutInSeconds)) {
                unset($this->storage[$identifier]);
            }
        }
    }

    public function purgeAll(): void
    {
        $this->storage = [];
    }
}
