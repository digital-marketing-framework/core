<?php

namespace DigitalMarketingFramework\Core\Cache;

interface CacheInterface {
    public function setTimeoutInSeconds(int $timeout): void;
    public function getTimeoutInSeconds(): int;

    /**
     * @return ?array<mixed>
     */
    public function fetch(string $identifier): ?array;

    /**
     * @param array<string> $tags
     * @return array<array<mixed>>
     */
    public function fetchByTags(array $tags): array;

    /**
     * @param array<mixed> $data
     * @param array<string> $tags
     */
    public function store(string $identifier, array $data, array $tags = []): void;
    
    public function purge(string $identifier): void;
    
    /**
     * @param array<string> $tags
     */
    public function purgeByTags(array $tags): void;
    
    public function purgeExpired(): void;
    
    public function purgeAll(): void;
}
