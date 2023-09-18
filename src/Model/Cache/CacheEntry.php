<?php

namespace DigitalMarketingFramework\Core\Model\Cache;

use DateTime;

class CacheEntry implements CacheEntryInterface
{
    /**
     * @param array<mixed> $data,
     * @param array<string> $tags
     */
    public function __construct(
        protected string $key,
        protected array $data,
        protected array $tags = [],
        protected DateTime $timestamp = new DateTime(),
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function updateTimestamp(): void
    {
        $this->setTimestamp(new DateTime());
    }

    public function expired(int $timeoutInSeconds = 86400): bool
    {
        $now = new DateTime();

        return $now->getTimestamp() - $this->getTimestamp()->getTimestamp() < $timeoutInSeconds;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function addTag(string $tag): void
    {
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
    }

    public function removeTag(string $tag): void
    {
        $this->tags = array_filter($this->tags, static function (string $entryTag) use ($tag) {
            return $entryTag !== $tag;
        });
    }
}
