<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class InsertDataContentResolver extends ContentResolver
{
    public function finish(string|ValueInterface|null &$result): bool
    {
        if ($this->configuration && $result !== null) {
            $result = GeneralUtility::parseSeparatorString($result);
            $matches = [];
            if (preg_match('/^\\{([^\\}]+)\\}$/', $result, $matches)) {
                $result = $this->getFieldValue($matches[1]);
            } else {
                foreach (array_keys($this->context->getData()->toArray()) as $key) {
                    if (strpos($result, '{' . $key . '}') !== false) {
                        $result = str_replace('{' . $key . '}', $this->getFieldValue($key), $result);
                    }
                }
                $result = preg_replace('/\\{[-_a-zA-Z0-9]+\\}/', '', $result);
            }
        }
        return false;
    }
}
