<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Crypto\HashServiceInterface;

interface CryptoRegistryInterface
{
    public function getHashService(): HashServiceInterface;

    public function setHashService(HashServiceInterface $hashService): void;
}
