<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class MapContentResolver extends ContentResolver
{
    protected const WEIGHT = 100;

    public function finish(string|ValueInterface|null &$result): bool
    {
        if ($result !== null) {
            $result = $this->resolveValueMap($this->configuration, $result);
        }
        return false;
    }
}
