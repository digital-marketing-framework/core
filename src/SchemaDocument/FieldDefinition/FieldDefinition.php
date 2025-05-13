<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition;

class FieldDefinition
{
    public const TYPE_STRING = 'STRING';

    public const TYPE_INTEGER = 'INTEGER';

    public const TYPE_BOOLEAN = 'BOOLEAN';

    public const TYPE_UNKNOWN = 'UNKNOWN';

    public const DEDICATED_COLLECTOR_FIELD = 'COLLECTOR_FIELD';

    /**
     * @param ?array<string|int|bool|array<mixed>> $values
     */
    public function __construct(
        protected string $name,
        protected string $type = self::TYPE_UNKNOWN,
        protected string $label = '',
        protected ?bool $multiValue = null,
        protected ?string $dedicated = null,
        protected ?array $values = null,
        protected ?bool $required = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabel(): string
    {
        return $this->label !== '' ? $this->label : $this->getName();
    }

    public function isMultiValue(): ?bool
    {
        return $this->multiValue;
    }

    public function dedicatedField(): ?string
    {
        return $this->dedicated;
    }

    /**
     * @return ?array<mixed>
     */
    public function getValues(): ?array
    {
        return $this->values;
    }

    public function isRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * @return array{name:string,type:string,label:string,multiValue?:bool,dedicated?:string,values?:array<mixed>,required?:bool}
     */
    public function toArray(): array
    {
        $result = [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'label' => $this->getLabel(),
        ];

        $multiValue = $this->isMultiValue();
        if ($multiValue !== null) {
            $result['multiValue'] = $multiValue;
        }

        $dedicated = $this->dedicatedField();
        if ($dedicated !== null) {
            $result['dedicated'] = $dedicated;
        }

        $values = $this->getValues();
        if ($values !== null) {
            $result['values'] = $values;
        }

        $required = $this->isRequired();
        if ($required !== null) {
            $result['required'] = $required;
        }

        return $result;
    }

    public function merge(FieldDefinition $fieldDefinition): void
    {
        if ($this->getType() !== $fieldDefinition->getType()) {
            $this->type = static::TYPE_UNKNOWN;
        }

        if ($fieldDefinition->dedicatedField() !== $this->dedicatedField()) {
            $this->dedicated = null;
        }

        if ($fieldDefinition->isMultiValue() !== $this->isMultiValue()) {
            $this->multiValue = null;
        }

        if ($fieldDefinition->isRequired() !== $this->isRequired()) {
            $this->required = null;
        }

        foreach ($fieldDefinition->getValues() as $value) {
            if (!in_array($value, $this->values, true)) {
                $this->values[] = $value;
            }
        }
    }
}
