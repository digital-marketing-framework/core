<?php

namespace DigitalMarketingFramework\Core\Model\Indentifier;

abstract class CacheIdentifier implements CacheIdentifierInterface
{
    abstract protected function getInternalCacheKey(): string;

    public function __toString(): string
    {
        return $this->getCacheKey();
    }

    public function getCacheKey(): string
    {
        return $this->getDomainIdentifier() . ':' . $this->getInternalCacheKey();
    }
}
