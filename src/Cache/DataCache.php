<?php

namespace DigitalMarketingFramework\Core\Cache;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;

class DataCache implements DataCacheInterface
{
    protected const PREFIX = 'digital_marketing_framework';

    protected const CACHE_KEY_DATA = 'data';

    protected const CACHE_KEY_REFERENCE = 'reference';

    protected CacheInterface $cache;

    public function __construct(CacheInterface|NonTaggableCacheInterface $cache)
    {
        if (!$cache instanceof CacheInterface) {
            $cache = new TaggableCacheWrapper($cache);
        }

        $this->cache = $cache;
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
     * @param ?array{reference?:string,data?:array<string,mixed>} $result
     * @param array<string> $processedKeys
     */
    protected function resolveResult(?array $result, array $processedKeys = []): ?DataInterface
    {
        if (isset($result[static::CACHE_KEY_REFERENCE])) {
            /** @var string */
            $reference = $result[static::CACHE_KEY_REFERENCE];

            $loopFound = in_array($reference, $processedKeys);
            $processedKeys[] = $reference;
            if ($loopFound) {
                throw new DigitalMarketingFrameworkException('Cache reference loop found: ' . implode(',', $processedKeys));
            }

            $result = $this->cache->fetch($this->addKeyPrefix($reference));

            return $this->resolveResult($result, $processedKeys);
        }

        if (isset($result[static::CACHE_KEY_DATA])) {
            return Data::unpack($result[static::CACHE_KEY_DATA]);
        }

        return null;
    }

    /**
     * @param array<string> $tags
     *
     * @return array<string>
     */
    protected function addTagPrefix(array $tags): array
    {
        return array_unique(array_map(static function (string $tag) {
            if ($tag === static::PREFIX) {
                return $tag;
            }

            return static::PREFIX . '-' . $tag;
        }, $tags));
    }

    protected function addKeyPrefix(string $key): string
    {
        return static::PREFIX . '-' . $key;
    }

    /**
     * @param array<array<mixed>> $results
     *
     * @return array<DataInterface>
     */
    protected function resolveResults(array $results): array
    {
        $resolvedResults = [];
        foreach ($results as $result) {
            $resolvedResult = $this->resolveResult($result);
            if ($resolvedResult instanceof DataInterface) {
                $resolvedResults[] = $resolvedResult;
            }
        }

        return $resolvedResults;
    }

    /**
     * @param array<string> $processedKeys
     */
    protected function followReferenceKeys(string $key, array $processedKeys = []): string
    {
        $loopFound = in_array($key, $processedKeys);
        $processedKeys[] = $key;
        if ($loopFound) {
            throw new DigitalMarketingFrameworkException('Cache reference loop found: ' . implode(',', $processedKeys));
        }

        $result = $this->cache->fetch($key);
        if (isset($result[static::CACHE_KEY_REFERENCE])) {
            return $this->followReferenceKeys($result[static::CACHE_KEY_REFERENCE], $processedKeys);
        }

        return $key;
    }

    public function fetch(IdentifierInterface $identifier): ?DataInterface
    {
        $key = $this->addKeyPrefix($identifier->getCacheKey());
        $result = $this->cache->fetch($key);

        return $this->resolveResult($result, [$key]);
    }

    public function fetchMultiple(array $identifiers): array
    {
        $results = $this->cache->fetchMultiple(
            array_map(
                function (IdentifierInterface $identifier) {
                    return $this->addKeyPrefix($identifier->getCacheKey());
                },
                $identifiers
            )
        );

        return $this->resolveResults($results);
    }

    /**
     * @param array<string> $tags
     */
    public function store(IdentifierInterface $identifier, DataInterface $data, array $tags = [], bool $followReferences = false): void
    {
        $key = $this->addKeyPrefix($identifier->getCacheKey());
        if ($followReferences) {
            $key = $this->followReferenceKeys($key);
        }

        $tags[] = static::PREFIX;
        $tags[] = $identifier->getDomainKey();
        $this->cache->store(
            $key,
            [static::CACHE_KEY_DATA => $data->pack()],
            $this->addTagPrefix($tags)
        );
    }

    /**
     * @param array<string> $tags
     */
    public function storeReference(IdentifierInterface $source, IdentifierInterface $target, array $tags = []): void
    {
        $tags[] = static::PREFIX;
        $tags[] = $source->getDomainKey();
        if ($source->getDomainKey() !== $target->getDomainKey()) {
            $tags[] = $target->getDomainKey();
        }

        $this->cache->store(
            $this->addKeyPrefix($source->getCacheKey()),
            [static::CACHE_KEY_REFERENCE => $target->getCacheKey()],
            $this->addTagPrefix($tags)
        );
    }

    public function purge(IdentifierInterface $identifier): void
    {
        $this->cache->purge(
            $this->addKeyPrefix($identifier->getCacheKey())
        );
    }

    /**
     * @param array<string> $tags
     */
    public function purgeByTags(array $tags): void
    {
        $this->cache->purgeByTags(
            $this->addTagPrefix($tags)
        );
    }

    public function purgeExpired(): void
    {
        $this->cache->purgeExpired();
    }

    public function purgeAll(): void
    {
        $this->cache->purgeByTags([static::PREFIX]);
    }
}
