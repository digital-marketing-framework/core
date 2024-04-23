<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition;

class FieldListDefinition
{
    /**
     * @param array<string,FieldDefinition> $fields
     */
    public function __construct(
        protected string $name,
        protected array $fields = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getField(string $name): ?FieldDefinition
    {
        return $this->fields[$name] ?? null;
    }

    public function addField(FieldDefinition $fieldDefinition): void
    {
        $this->fields[$fieldDefinition->getName()] = $fieldDefinition;
    }

    public function removeField(string $name): void
    {
        if (array_key_exists($name, $this->fields)) {
            unset($this->fields[$name]);
        }
    }

    /**
     * @return array<string,array{name:string,type:string,label:string,multiValue:?bool}>
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->fields as $name => $field) {
            $result[$name] = $field->toArray();
        }

        return $result;
    }
}
