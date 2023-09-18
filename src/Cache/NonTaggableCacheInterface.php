<?php

namespace DigitalMarketingFramework\Core\Cache;

interface NonTaggableCacheInterface
{
    public function setTimeoutInSeconds(int $timeout): void;

    public function getTimeoutInSeconds(): int;

    /**
     * @return ?array<mixed>
     */
    public function fetch(string $key): ?array;

    /**
     * @param array<string> $keys
     *
     * @return array<array<mixed>>
     */
    public function fetchMultiple(array $keys): array;

    /**
     * @param array<mixed> $data
     */
    public function store(string $key, array $data): void;

    public function purge(string $key): void;

    /**
     * @param array<string> $keys
     */
    public function purgeMultiple(array $keys): void;

    public function purgeExpired(): void;

    public function purgeAll(): void;
}
