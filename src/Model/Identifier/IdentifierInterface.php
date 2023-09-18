<?php

namespace DigitalMarketingFramework\Core\Model\Identifier;

interface IdentifierInterface
{
    public function getDomainKey(): string;

    public function getCacheKey(): string;

    /**
     * @return array<mixed>
     */
    public function getPayload(): array;
}
