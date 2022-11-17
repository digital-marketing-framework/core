<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

class MapContentResolver extends ContentResolver
{
    protected const WEIGHT = 100;

    public function finish(&$result): bool
    {
        if ($result !== null) {
            $result = $this->resolveValueMap($this->configuration, $result);
        }
        return false;
    }
}
