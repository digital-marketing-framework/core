<?php

namespace DigitalMarketingFramework\Core\Model\Indentifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

interface CacheIdentifierInterface extends ValueInterface
{
    public function getDomainIdentifier(): string;
    public function getCacheKey(): string;
}
