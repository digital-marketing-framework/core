<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class FirstOfContentResolver extends ContentResolver
{
    public function build(): string|ValueInterface|null
    {
        ksort($this->configuration, SORT_NUMERIC);
        $result = null;
        foreach ($this->configuration as $valueConfiguration) {
            $value = $this->resolveContent($valueConfiguration);
            if ($value !== null) {
                $result = $value;
            }
            if (!GeneralUtility::isEmpty($result)) {
                break;
            }
        }
        return $result;
    }
}
