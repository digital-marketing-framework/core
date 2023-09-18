<?php

namespace DigitalMarketingFramework\Core\Model\Cache;

use DateTime;

interface CacheEntryInterface
{
    public function getKey(): string;

    public function setKey(string $key): void;

    /**
     * @return array<mixed>
     */
    public function getData(): array;

    /**
     * @param array<mixed> $data
     */
    public function setData(array $data): void;

    public function getTimestamp(): DateTime;

    public function setTimestamp(DateTime $timestamp): void;

    public function updateTimestamp(): void;

    public function expired(int $timeoutInSeconds = 86400): bool;

    /**
     * @return array<string>
     */
    public function getTags(): array;

    /**
     * @param array<string> $tags
     */
    public function setTags(array $tags): void;

    public function addTag(string $tag): void;

    public function removeTag(string $tag): void;
}
