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

    public function fieldExists(string $name): bool
    {
        return array_key_exists($name, $this->fields);
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
        return array_map(static fn ($field) => $field->toArray(), $this->fields);
    }

    /**
     * @return array<FieldDefinition>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function merge(FieldListDefinition $fieldListDefinition): void
    {
        foreach ($fieldListDefinition->getFields() as $name => $field) {
            $myField = $this->fields[$name] ?? null;
            if ($myField instanceof FieldDefinition) {
                $myField->merge($field);
            } else {
                $this->addField($field);
            }
        }
    }
}
