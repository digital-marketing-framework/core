<?php

namespace DigitalMarketingFramework\Core\Cache;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;

interface DataCacheInterface
{
    public function setTimeoutInSeconds(int $timeout): void;

    public function getTimeoutInSeconds(): int;

    public function fetch(IdentifierInterface $identifier): ?DataInterface;

    /**
     * @param array<IdentifierInterface> $identifiers
     *
     * @return array<DataInterface>
     */
    public function fetchMultiple(array $identifiers): array;

    /**
     * @param array<string> $tags
     */
    public function store(IdentifierInterface $identifier, DataInterface $data, array $tags = [], bool $followReferences = false): void;

    /**
     * @param array<string> $tags
     */
    public function storeReference(IdentifierInterface $source, IdentifierInterface $target, array $tags = []): void;

    public function purge(IdentifierInterface $identifier): void;

    /**
     * @param array<string> $tags
     */
    public function purgeByTags(array $tags): void;

    public function purgeExpired(): void;

    public function purgeAll(): void;
}
