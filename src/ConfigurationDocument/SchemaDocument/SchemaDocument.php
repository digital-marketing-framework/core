<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class SchemaDocument
{
    public const FLATTEN_SCHEMA = true;

    /**
     * @param array<string,SchemaInterface> $customTypes
     * @param array<string,array<string|int|bool>>
     */
    public function __construct(
        protected ContainerSchema $mainSchema = new ContainerSchema(),
        protected array $customTypes = [],
        protected array $valueSets = [],
    ) {
    }

    public function setMainSchema(ContainerSchema $schema): void
    {
        $this->mainSchema = $schema;
    }

    public function getMainSchema(): ContainerSchema
    {
        return $this->mainSchema;
    }

    public function addCustomType(SchemaInterface $schema, ?string $type = null): void
    {
        if ($type === null) {
            $type = $schema->getCustomType();
        }
        $this->customTypes[$type] = $schema;
    }

    public function getCustomType(string $type): ?SchemaInterface
    {
        return $this->customTypes[$type] ?? null;
    }

    /**
     * @return array<string,SchemaInterface>
     */
    public function getCustomTypes(): array
    {
        return $this->customTypes;
    }

    public function getValueSets(): array
    {
        return $this->valueSets;
    }

    public function getValueSet(string $name): ?array
    {
        return $this->valueSets[$name] ?? null;
    }

    public function setValueSet(string $name, array $valueSet): void
    {
        $this->valueSets[$name] = $valueSet;
    }

    public function removeValueSet(string $name): void
    {
        if (isset($this->valueSets[$name])) {
            unset($this->valueSets[$name]);
        }
    }

    public function addValueToValueSet(string $name, string|int|bool $value): void
    {
        $valueSet = $this->getValueSet($name) ?? [];
        if (!in_array($value, $valueSet, true)) {
            $valueSet[] = $value;
        }
        $this->setValueSet($name, $valueSet);
    }

    protected function filterSchemaDocument(array &$schemaDocument): void
    {
        foreach ($schemaDocument as $key => $value) {
            if ($value === null) {
                unset($schemaDocument[$key]);
            } elseif (is_array($value)) {
                $this->filterSchemaDocument($schemaDocument[$key]);
            }
        }
    }

    protected function getAllValueSets(): array
    {
        $valueSets = $this->valueSets;
        foreach ($this->mainSchema->getValueSets() as $name => $set) {
            if (!isset($valueSets[$name])) {
                $valueSets[$name] = $set;
            }
        }

        foreach ($this->customTypes as $customTypeSchema) {
            foreach ($customTypeSchema->getValueSets() as $name => $set) {
                if (!isset($valueSets[$name])) {
                    $valueSets[$name] = $set;
                }
            }
        }

        return $valueSets;
    }

    public function toArray(): array
    {
        $schemaDocument = [
            'valueSets' => $this->getAllValueSets(),
            'types' => [],
            'schema' => $this->mainSchema->toArray(),
        ];
        foreach ($this->customTypes as $type => $customTypeSchema) {
            $schemaDocument['types'][$type] = $customTypeSchema->toArray();
        }
        $this->filterSchemaDocument($schemaDocument);
        return $schemaDocument;
    }
}
