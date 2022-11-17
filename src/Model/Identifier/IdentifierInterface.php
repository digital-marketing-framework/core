<?php

namespace DigitalMarketingFramework\Core\Model\Identifier;

interface IdentifierInterface
{
    public function getDomainKey(): string;
    public function getCacheKey(): string;
    public function getPayload(): array;
}
