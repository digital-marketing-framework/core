<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DefaultContentResolver extends ContentResolver
{
    protected const WEIGHT = 100;

    public function finish(string|ValueInterface|null &$result): bool
    {
        if (GeneralUtility::isEmpty($result)) {
            $default = $this->resolveContent($this->configuration, $this->context);
            if ($default !== null) {
                $result = $default;
            }
        }
        return false;
    }
}
