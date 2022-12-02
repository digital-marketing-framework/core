<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class SelfContentResolver extends ContentResolver
{
    protected const WEIGHT = 0;

    public function build(): string|ValueInterface|null
    {
        if ($this->configuration instanceof ValueInterface || $this->configuration === null) {
            return $this->configuration;
        }
        return (string)$this->configuration;
    }
}
