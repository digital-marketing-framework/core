<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value\ScalarValues;

abstract class ScalarValueSchema extends Schema
{
    public function __construct(
        mixed $defaultValue = null,
        protected ScalarValues $allowedValues = new ScalarValues(),
        protected ScalarValues $suggestedValues = new ScalarValues(),
        protected mixed $value = null,
    ) {
        parent::__construct($defaultValue);
    }

    public function getAllowedValues(): ScalarValues
    {
        return $this->allowedValues;
    }

    public function getSuggestedValues(): ScalarValues
    {
        return $this->suggestedValues;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    protected function getConfig(): array
    {
        return parent::getConfig() + [
            'value' => $this->value,
            'allowedValues' => $this->allowedValues->toArray(),
            'suggestedValues' => $this->suggestedValues->toArray(),
        ];
    }

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed
    {
        $defaultValue = parent::getDefaultValue($schemaDocument);
        if ($defaultValue !== null) {
            return $defaultValue;
        }

        $allowedValues = $this->allowedValues->getValues($schemaDocument);
        if ($allowedValues !== []) {
            $rawValues = array_keys($allowedValues);

            return reset($rawValues);
        }

        return null;
    }
}
