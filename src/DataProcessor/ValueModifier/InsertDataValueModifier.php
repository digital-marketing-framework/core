<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class InsertDataValueModifier extends ValueModifier
{
    public const WEIGHT = 0;

    /**
     * Matches a value that consists of nothing but a single field reference.
     * "${var}" cannot match it, because the leading "$" falls outside the anchors.
     */
    protected const PATTERN_WHOLE_VALUE_FIELD = '/^\{([^}]+)\}$/';

    /**
     * Matches every placeholder, capturing the prefix that selects its source:
     * no prefix for a submission field, "$" for a configuration variable.
     * Note that "@{...}" is reserved for environment variables in global settings
     * and is deliberately not resolved here.
     */
    protected const PATTERN_PLACEHOLDER = '/(\$?)\{([^}]+)\}/';

    protected const PREFIX_VARIABLE = '$';

    protected function modifyValue(string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value === null) {
            return null;
        }

        $result = GeneralUtility::parseSeparatorString($value);

        // A value that is nothing but a field reference returns the field value itself,
        // so that multi-value fields survive as ValueInterface instead of being cast to string.
        $matches = [];
        if (preg_match(static::PATTERN_WHOLE_VALUE_FIELD, $result, $matches)) {
            return $this->getFieldValue($matches[1]);
        }

        return preg_replace_callback(
            static::PATTERN_PLACEHOLDER,
            function (array $matches): string {
                $name = $matches[2];

                if ($matches[1] === static::PREFIX_VARIABLE) {
                    $value = $this->getVariableValue($name);
                    if ($value === null) {
                        $this->logger->warning(sprintf('Insert data: variable "%s" is not declared.', $name));

                        return '';
                    }

                    return $value;
                }

                // A missing field is a routine case - an optional field that was not
                // submitted - and resolves to an empty string without a log entry.
                return (string)($this->getFieldValue($name) ?? '');
            },
            $result
        );
    }
}
