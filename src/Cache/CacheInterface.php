<?php

namespace DigitalMarketingFramework\Core\Cache;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Indentifier\CacheIdentifierInterface;

interface CacheInterface {
    public function setTimeoutInSeconds(int $timeout): void;
    public function getTimeoutInSeconds(): int;

    public function fetch(CacheIdentifierInterface $identifier): ?DataInterface;

    /**
     * @param array<string> $tags
     * @return array<DataInterface>
     */
    public function fetchByTags(array $tags): array;

    /**
     * @param array<string> $tags
     */
    public function store(CacheIdentifierInterface $identifier, DataInterface $data, array $tags = []): void;
    
    public function purge(CacheIdentifierInterface $identifier): void;
    
    /**
     * @param array<string> $tags
     */
    public function purgeByTags(array $tags): void;
    
    public function purgeExpired(): void;
    
    public function purgeAll(): void;
}
