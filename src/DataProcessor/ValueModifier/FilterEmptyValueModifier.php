<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class FilterEmptyValueModifier extends ValueModifier
{
    public function modify(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if (!$this->proceed()) {
            return $value;
        }

        // an empty single value resolves to null, which makes its parent (if any) discard it
        if (!$value instanceof MultiValueInterface) {
            return GeneralUtility::isEmpty($value) ? null : $value;
        }

        $filteredValues = [];
        foreach ($value as $index => $subValue) {
            $filteredSubValue = $this->modify($subValue);
            if ($filteredSubValue === null) {
                continue;
            }

            // numeric indices are renumbered so that discarded values do not leave gaps behind,
            // while named indices keep their meaning and are preserved
            if (is_int($index)) {
                $filteredValues[] = $filteredSubValue;
            } else {
                $filteredValues[$index] = $filteredSubValue;
            }
        }

        $multiValueClass = $value::class;
        $filteredValue = new $multiValueClass($filteredValues);
        $filteredValue->setGlue($value->getGlue());

        return $filteredValue;
    }
}
