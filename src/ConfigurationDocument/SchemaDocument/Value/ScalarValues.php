<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class ScalarValues
{
    protected ValueSet $list;

    /**
     * @param array<string|int|bool,string> $list
     * @param array<string> $sets
     * @param array<string> $references
     */
    public function __construct(
        array $list = [],
        protected array $sets = [],
        protected array $references = [],
    ) {
        $this->list = new ValueSet($list);
    }

    public function addValue(string|int|bool $value, ?string $label = null): void
    {
        $this->list->addValue($value, $label);
    }

    public function addValueSet(string $name): void
    {
        $this->sets[] = $name;
    }

    public function addReference(string $reference): void
    {
        $this->references[] = $reference;
    }

    public function reset(): void
    {
        $this->list = [];
        $this->sets = [];
        $this->references = [];
    }

    public function toArray(): ?array
    {
        $list = $this->list->toArray();
        if (empty($list) && empty($this->sets) && empty($this->references)) {
            return null;
        }
        return [
            'list' => !empty($list) ? $list : null,
            'sets' => !empty($this->sets) ? $this->sets : null,
            'references' => !empty($this->references) ? $this->references : null,
        ];
    }

    public function getFirstValue(SchemaDocument $schemaDocument): string|int|bool|null
    {
        $values = $this->getValues($schemaDocument);
        if (empty($values)) {
            return null;
        }
        return reset(array_keys($values));
    }

    /**
     * @return array<string|int|bool,string>
     */
    public function getValues(SchemaDocument $schemaDocument): array
    {
        $values = $this->list;
        foreach ($this->sets as $setName) {
            $set = $schemaDocument->getValueSet($setName) ?? new ValueSet();
            $values->merge($set);
            // NOTE references can't be calculated without a data object to be referenced, so we do not take those into account
            // TODO do we need to do something about this? probably, because server-side evaluation is supposed to use the schema
            //      however, if we evaluate a data object, then we have it to follow the references
            //      maybe add a second parameter for the data object, optional
        }
        return $values->toArray();
    }
}
