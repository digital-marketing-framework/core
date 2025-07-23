<?php

namespace DigitalMarketingFramework\Core\Crypto;

interface HashServiceAwareInterface
{
    public function setHashService(HashServiceInterface $hashService): void;
}
