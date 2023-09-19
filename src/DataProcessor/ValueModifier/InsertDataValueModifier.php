<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class InsertDataValueModifier extends ValueModifier
{
    public const WEIGHT = 0;

    protected function modifyValue(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if ($value === null) {
            return null;
        }

        $result = GeneralUtility::parseSeparatorString($value);
        $matches = [];
        if (preg_match('/^\\{([^\\}]+)\\}$/', $result, $matches)) {
            $result = $this->getFieldValue($matches[1]);
        } else {
            foreach (array_keys($this->context->getData()->toArray()) as $key) {
                if (str_contains($result, '{' . $key . '}')) {
                    $result = str_replace('{' . $key . '}', $this->getFieldValue($key), $result);
                }
            }

            $result = preg_replace('/\\{[-_a-zA-Z0-9]+\\}/', '', $result);
        }

        return $result;
    }
}
