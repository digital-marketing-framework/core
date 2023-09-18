<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value;

use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class ValueSet
{
    /**
     * @param array<string|int|bool,string> $values
     */
    public function __construct(
        protected array $values = [],
    ) {
    }

    public function addValue(string|int|bool $value, ?string $label = null): void
    {
        $this->values[$value] = $label ?? GeneralUtility::getLabelFromValue($value);
    }

    public function merge(ValueSet $valueSet): void
    {
        foreach ($valueSet->toArray() as $value => $label) {
            $this->addValue($value, $label);
        }
    }

    /**
     * @return array<string|int|bool,string>
     */
    public function toArray(): array
    {
        return $this->values;
    }
}
