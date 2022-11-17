<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

class IgnoreContentResolver extends ContentResolver
{
    protected const WEIGHT = 0;

    protected function ignore($result): bool
    {
        return (bool)$this->configuration;
    }

    public function finish(&$result): bool
    {
        if ($this->ignore($result)) {
            $result = null;
            return true;
        }
        return false;
    }
}
