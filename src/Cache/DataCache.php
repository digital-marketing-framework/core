<?php

namespace DigitalMarketingFramework\Core\Cache;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;

class DataCache implements DataCacheInterface
{
    protected const CACHE_KEY_DATA = 'data';
    protected const CACHE_KEY_REFERENCE = 'reference';

    public function __construct(
        protected CacheInterface $cache,
    ){
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
     * @param ?array<mixed> $result
     * @param array<string> $processedKeys
     */
    protected function resolveResult(?array $result, array $processedKeys = []): ?DataInterface
    {
        if (isset($result[static::CACHE_KEY_REFERENCE])) {
            $reference = $result[static::CACHE_KEY_REFERENCE];

            $loopFound = in_array($reference, $processedKeys);
            $processedKeys[] = $reference;
            if ($loopFound) {
                throw new DigitalMarketingFrameworkException('Cache reference loop found: ' . implode(',', $processedKeys));
            }
            
            $result = $this->cache->fetch($reference);
            return $this->resolveResult($result, $processedKeys);
        }

        if (isset($result[static::CACHE_KEY_DATA])) {
            return Data::unpack($result[static::CACHE_KEY_DATA]);
        }

        return null;
    }

    /**
     * @param array<array<mixed>> $results
     * @return array<array<DataInterface>>
     */
    protected function resolveResults(array $results): array
    {
        $resolvedResults = [];
        foreach ($results as $result) {
            $resolvedResult = $this->resolveResult($result);
            if ($resolvedResult !== null) {
                $resolvedResults[] = $resolvedResult;
            }
        }
        return $resolvedResults;
    }

    protected function followReferenceKeys(IdentifierInterface|string $key, array $processedKeys = []): string
    {
        if ($key instanceof IdentifierInterface) {
            $key = $key->getCacheKey();
        }

        if (in_array($key, $processedKeys)) {
            throw new DigitalMarketingFrameworkException('Cache reference loop found: ' . implode(',', $processedKeys));
        }
        $processedKeys[] = $key;
        
        $result = $this->cache->fetch($key);
        if (isset($result[static::CACHE_KEY_REFERENCE])) {
            return $this->followReferenceKeys($result[static::CACHE_KEY_DATA], $processedKeys);
        }
        
        return $key;
    }

    public function fetch(IdentifierInterface $identifier): ?DataInterface
    {
        $key = $identifier->getCacheKey();
        $result = $this->cache->fetch($key);
        return $this->resolveResult($result, [$key]);
    }

    public function fetchByKeys(array $identifiers): array
    {
        $results = $this->cache->fetchByKeys(
            array_map(function(IdentifierInterface $identifier) {
                return $identifier->getCacheKey();
            }, 
            $identifiers)
        );
        return $this->resolveResults($results);
    }

    public function fetchByTags(array $tags): array
    {
        $results = $this->cache->fetchByTags($tags);
        return $this->resolveResults($results);
    }

    /**
     * @param array<string> $tags
     */
    public function store(IdentifierInterface $identifier, DataInterface $data, array $tags = [], bool $followReferences = false): void
    {
        $key = $followReferences ? $this->followReferenceKeys($identifier) : $identifier->getCacheKey();
        $tags[] = $identifier->getDomainKey();
        $this->cache->store($key, [static::CACHE_KEY_DATA => $data->pack()], $tags);
    }

    /**
     * @param array<string> $tags
     */
    public function storeReference(IdentifierInterface $source, IdentifierInterface $target, array $tags = []): void
    {
        $tags[] = $source->getDomainKey();
        $tags[] = $target->getDomainKey();
        $this->cache->store($source->getCacheKey(), [static::CACHE_KEY_REFERENCE => $target->getCacheKey()], $tags);
    }

    public function purge(string $key): void
    {
        $this->cache->purge($key);
    }
    
    /**
     * @param array<string> $tags
     */
    public function purgeByTags(array $tags): void
    {
        $this->cache->purgeByTags($tags);
    }
    
    public function purgeExpired(): void
    {
        $this->cache->purgeExpired();
    }
    
    public function purgeAll(): void
    {
        $this->cache->purgeAll();
    }
}
