<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Crypto\HashServiceInterface;

trait CryptoRegistryTrait
{
    protected HashServiceInterface $hashService;

    public function getHashService(): HashServiceInterface
    {
        return $this->hashService;
    }

    public function setHashService(HashServiceInterface $hashService): void
    {
        $this->hashService = $hashService;
    }
}
