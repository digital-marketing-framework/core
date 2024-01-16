<?php

namespace DigitalMarketingFramework\Core\Model\Identifier;

use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class Identifier implements IdentifierInterface
{
    /**
     * @param array<mixed> $payload
     */
    public function __construct(
        protected array $payload = [],
    ) {
    }

    // TODO should the internal identifiers (like a visitor ID or an email address) be hashed when used as cache key?
    abstract protected function getInternalCacheKey(): string;

    public function getDomainKey(): string
    {
        // TODO shouldn't the domain key come from the data collector instead of the identifier (collector)?
        return GeneralUtility::getPluginKeyword(static::class, IdentifierInterface::class);
    }

    public function getCacheKey(): string
    {
        return $this->getDomainKey() . '-' . $this->getInternalCacheKey();
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
