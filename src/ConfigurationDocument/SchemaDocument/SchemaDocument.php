<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value\ValueSet;

class SchemaDocument
{
    public static bool $flattenSchema = true;

    /** @var array<string,string> */
    protected array $version = [];

    /**
     * @param array<string,SchemaInterface> $customTypes
     * @param array<string,ValueSet> $valueSets
     */
    public function __construct(
        protected ContainerSchema $mainSchema = new ContainerSchema(),
        protected array $customTypes = [],
        protected array $valueSets = [],
    ) {
    }

    /**
     * @return array<string,string>
     */
    public function getVersion(): array
    {
        return $this->version;
    }

    public function addVersion(string $key, string $version): void
    {
        $this->version[$key] = $version;
    }

    public function setMainSchema(ContainerSchema $schema): void
    {
        $this->mainSchema = $schema;
    }

    public function getMainSchema(): ContainerSchema
    {
        return $this->mainSchema;
    }

    public function addCustomType(SchemaInterface $schema, string $type): void
    {
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

    /**
     * @return array<string,ValueSet>
     */
    public function getValueSets(): array
    {
        return $this->valueSets;
    }

    public function getValueSet(string $name): ?ValueSet
    {
        return $this->getAllValueSets()[$name] ?? null;
    }

    public function setValueSet(string $name, ValueSet $valueSet): void
    {
        $this->valueSets[$name] = $valueSet;
    }

    public function removeValueSet(string $name): void
    {
        if (isset($this->valueSets[$name])) {
            unset($this->valueSets[$name]);
        }
    }

    public function addValueToValueSet(string $name, string|int|bool $value, ?string $label = null): void
    {
        if (!isset($this->valueSets[$name])) {
            $this->valueSets[$name] = new ValueSet();
        }

        $this->valueSets[$name]->addValue($value, $label);
    }

    /**
     * @param array<string,mixed> $schemaDocument
     */
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

    /**
     * @return array<string,ValueSet>
     */
    protected function getAllValueSets(): array
    {
        $valueSets = $this->valueSets;
        foreach ($this->mainSchema->getValueSets() as $name => $set) {
            if (!isset($valueSets[$name])) {
                $valueSets[$name] = $set;
            } else {
                $valueSets[$name]->merge($set);
            }
        }

        foreach ($this->customTypes as $customTypeSchema) {
            foreach ($customTypeSchema->getValueSets() as $name => $set) {
                if (!isset($valueSets[$name])) {
                    $valueSets[$name] = $set;
                } else {
                    $valueSets[$name]->merge($set);
                }
            }
        }

        return $valueSets;
    }

    /**
     * @return array{valueSets:array<string,array<string,string>>,types:array<string,array<string,mixed>>,schema:array<string,mixed>}
     */
    public function toArray(): array
    {
        $schemaDocument = [
            'valueSets' => [],
            'types' => [],
            'schema' => $this->mainSchema->toArray(),
        ];
        foreach ($this->getAllValueSets() as $name => $set) {
            $schemaDocument['valueSets'][$name] = $set->toArray();
        }

        foreach ($this->customTypes as $type => $customTypeSchema) {
            $schemaDocument['types'][$type] = $customTypeSchema->toArray();
        }

        $this->filterSchemaDocument($schemaDocument);

        return $schemaDocument;
    }

    public function getDefaultValue(?SchemaInterface $schema = null): mixed
    {
        if (!$schema instanceof SchemaInterface) {
            $schema = $this->mainSchema;
        }

        return $schema->getDefaultValue($this);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function preSaveDataTransform(array &$data, ?SchemaInterface $schema = null): void
    {
        if (!$schema instanceof SchemaInterface) {
            $schema = $this->mainSchema;
        }

        $schema->preSaveDataTransform($data, $this);
    }
}
