<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class IgnoreContentResolver extends ContentResolver
{
    protected const WEIGHT = 0;

    protected function ignore($result): bool
    {
        return (bool)$this->configuration;
    }

    public function finish(string|ValueInterface|null &$result): bool
    {
        if ($this->ignore($result)) {
            $result = null;
            return true;
        }
        return false;
    }
}
