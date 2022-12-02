<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IndexContentResolver extends ContentResolver
{
    public function finish(string|ValueInterface|null &$result): bool
    {
        $processedIndex = $this->resolveContent($this->configuration);
        if ($processedIndex !== null) {
            $indices = GeneralUtility::castValueToArray($processedIndex);
            while (!empty($indices)) {
                $index = array_shift($indices);
                if ($result instanceof MultiValueInterface && isset($result[$index])) {
                    $result = $result[$index];
                } else {
                    $result = null;
                    break;
                }
            }
        }
        return false;
    }
}
