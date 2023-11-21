<?php

namespace DigitalMarketingFramework\Core\Model\Identifier;

class EmailIdentifier extends Identifier
{
    public function __construct(string $email)
    {
        parent::__construct(['email' => $email]);
    }

    protected function getInternalCacheKey(): string
    {
        return $this->getEmail();
    }

    public function getEmail(): string
    {
        return $this->payload['email'];
    }
}
