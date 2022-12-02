<?php

namespace DigitalMarketingFramework\Core\Model\Cache;

use DateTime;

interface CacheEntryInterface
{
    public function getKey(): string;
    public function setKey(string $key): void;
    public function getData(): array;
    public function setData(array $data): void;
    public function getTimestamp(): DateTime;
    public function setTimestamp(DateTime $timestamp): void;
    public function updateTimestamp(): void;
    public function expired(int $timeoutInSeconds = 86400): bool;
    public function getTags(): array;
    public function setTags(array $tags): void;
    public function addTag(string $tag): void;
    public function removeTag(string $tag): void;
}
