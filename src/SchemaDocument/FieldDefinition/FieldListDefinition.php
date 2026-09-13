<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition;

class FieldListDefinition
{
    /**
     * @param array<string,FieldDefinition> $fields
     * @param ?string $label what the thing owning this context calls itself, when it is
     *                       registered from code and knows. A route's label says "Web-To-Lead"
     *                       where its keyword only says "salesforce".
     */
    public function __construct(
        protected string $name,
        protected array $fields = [],
        protected ?string $label = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getField(string $name): ?FieldDefinition
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * Combines the definition with any existing one for the same field name.
     *
     * See setField() for the case where a later source should win outright.
     */
    public function addField(FieldDefinition $fieldDefinition): void
    {
        $name = $fieldDefinition->getName();
        if (isset($this->fields[$name])) {
            $this->fields[$name]->merge($fieldDefinition);
        } else {
            $this->fields[$name] = $fieldDefinition;
        }
    }

    /**
     * Replaces any existing definition for this field name.
     *
     * Use this where a later source is authoritative, such as a stored definition correcting
     * one declared in code. addField() is for the opposite case, where each source knows only
     * part of the truth and their definitions should be combined.
     */
    public function setField(FieldDefinition $fieldDefinition): void
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
     * @return array<string,array{name:string,type:string,label:string,multiValue?:bool,dedicated?:string,values?:array<mixed>,required?:bool}>
     */
    public function toArray(): array
    {
        return array_map(static fn ($field) => $field->toArray(), $this->fields);
    }

    /**
     * @return array<string,FieldDefinition>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function merge(FieldListDefinition $fieldListDefinition): void
    {
        foreach ($fieldListDefinition->getFields() as $field) {
            $this->addField($field);
        }
    }
}
