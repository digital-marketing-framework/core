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

    abstract protected function getInternalCacheKey(): string;

    public function getDomainKey(): string
    {
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
