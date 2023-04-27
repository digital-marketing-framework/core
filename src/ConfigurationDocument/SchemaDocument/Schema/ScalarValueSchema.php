<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

abstract class ScalarValueSchema extends Schema
{
    public function __construct(
        protected mixed $defaultValue = null,
        protected ScalarValues $allowedValues = new ScalarValues(),
        protected ScalarValues $suggestedValues = new ScalarValues(),
        protected mixed $value = null,
    ) {
        parent::__construct();
    }

    public function getAllowedValues(): ScalarValues
    {
        return $this->allowedValues;
    }

    public function getSuggestedValues(): ScalarValues
    {
        return $this->suggestedValues;
    }

    public function setDefaultValue(mixed $value): void
    {
        $this->defaultValue = $value;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    protected function getConfig(): ?array
    {
        return [
            'value' => $this->value,
            'default' => $this->defaultValue,
            'allowedValues' => $this->allowedValues->toArray(),
            'suggestedValues' => $this->suggestedValues->toArray(),
        ];
    }
}
