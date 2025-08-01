<?php

namespace DigitalMarketingFramework\Core\Crypto;

/** @phpstan-ignore-next-line This trait can be used by other packages, even though it is not used in this one. */
trait HashServiceAwareTrait
{
    protected HashServiceInterface $hashService;

    public function setHashService(HashServiceInterface $hashService): void
    {
        $this->hashService = $hashService;
    }
}
