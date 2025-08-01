<?php

namespace DigitalMarketingFramework\Core\Crypto;

interface HashServiceInterface
{
    public function generateHash(string $subject, string $additionalSecret): string;

    public function validateHash(string $subject, string $additionalSecret, string $hash): bool;
}
