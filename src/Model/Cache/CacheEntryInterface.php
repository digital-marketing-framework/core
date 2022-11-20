<?php

namespace DigitalMarketingFramework\Core\Model\Cache;

use DateTime;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Indentifier\CacheIdentifierInterface;

interface CacheEntryInterface
{
    public function getIdentifier(): CacheIdentifierInterface;
    public function setIdentifier(CacheIdentifierInterface $identifier): void;
    public function getData(): DataInterface;
    public function setData(DataInterface $data): void;
    public function getTimestamp(): DateTime;
    public function setTimestamp(DateTime $timestamp): void;
    public function updateTimestamp(): void;
    public function expired(int $timeoutInSeconds = 86400): bool;
    public function getTags(): array;
    public function setTags(array $tags): void;
    public function addTag(string $tag): void;
    public function removeTag(string $tag): void;
}
