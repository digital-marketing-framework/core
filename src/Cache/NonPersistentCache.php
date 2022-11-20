<?php

namespace DigitalMarketingFramework\Core\Cache;

use DigitalMarketingFramework\Core\Model\Cache\CacheEntry;
use DigitalMarketingFramework\Core\Model\Cache\CacheEntryInterface;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Indentifier\CacheIdentifierInterface;

class NonPersistentCache implements CacheInterface
{
    /** @var array<string,CacheEntryInterface>*/
    protected array $storage;

    protected int $timeoutInSeconds = 86400;

    public function setTimeoutInSeconds(int $timeout): void
    {
        $this->timeoutInSeconds = $timeout;
    }

    public function getTimeoutInSeconds(): int
    {
        return $this->timeoutInSeconds;
    }

    public function fetch(CacheIdentifierInterface $identifier): ?DataInterface
    {
        $entry = $this->storage[$identifier->getCacheKey()] ?? null;
        if ($entry !== null) {
            if ($entry->expired($this->timeoutInSeconds)) {
                unset($this->storage[$identifier]);
                $entry = null;
            }
        }
        return $entry?->getData();
    }

    /**
     * @param array<string> $tags
     * @return array<string,CacheEntryInterface>
     */
    protected function fetchEntriesByTags(array $tags): array
    {
        $result = [];
        foreach ($this->storage as $identifier => $entry) {
            if ($entry->expired($this->timeoutInSeconds)) {
                unset($this->storage[$identifier]);
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
     * @return arrray<array<mixed>>
     */
    public function fetchByTags(array $tags): array
    {
        return array_map(function(CacheEntryInterface $entry) {
            return $entry->getData();
        }, $this->fetchEntriesByTags($tags));
    }

    /**
     * @param array<string> $tags
     */
    public function store(CacheIdentifierInterface $identifier, DataInterface $data, array $tags = []): void
    {
        $this->storage[$identifier] = new CacheEntry($identifier, $data, $tags);
    }
    
    public function purge(CacheIdentifierInterface $identifier): void
    {
        unset($this->storage[$identifier->getCacheKey()]);
    }

    /**
     * @param array<string> $tags
     */
    public function purgeByTags(array $tags): void
    {
        $entries = $this->fetchEntriesByTags($tags);
        foreach ($entries as $entry) {
            $this->purge($entry->getIdentifier());
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
