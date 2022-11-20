<?php

namespace DigitalMarketingFramework\Core\Model\Cache;

use DateTime;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Indentifier\CacheIdentifierInterface;

class CacheEntry implements CacheEntryInterface
{
    public function __construct(
        protected CacheIdentifierInterface $identifier,
        protected DataInterface $data,
        protected array $tags = [],
        protected DateTime $timestamp = new DateTime(),
    ) {
    }

    public function getIdentifier(): CacheIdentifierInterface
    {
        return $this->identifier;
    }

    public function setIdentifier(CacheIdentifierInterface $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getData(): DataInterface
    {
        return $this->data;
    }

    public function setData(DataInterface $data): void
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
        $this->tags = array_filter($this->tags, function(string $entryTag) use ($tag) {
            return $entryTag !== $tag;
        });
    }
}
