<?php

namespace DigitalMarketingFramework\Core\Model\Identifier;

class EmailIdentifier extends Identifier
{
    public function __construct(string $pluginKeyword, string $email)
    {
        parent::__construct([
            'pluginKeyword' => $pluginKeyword,
            'email' => $email,
        ]);
    }

    public function getEmail(): string
    {
        return $this->payload['email'];
    }

    protected function getInternalCacheKey(): string
    {
        return $this->payload['pluginKeyword'] . '-' . $this->payload['email'];
    }
}
