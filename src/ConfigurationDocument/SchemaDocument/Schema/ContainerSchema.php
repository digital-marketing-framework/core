<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

class ContainerSchema extends Schema
{
    /**
     * @var array<string,ContainerProperty>
     */
    protected array $properties = [];

    public function getType(): string
    {
        return "CONTAINER";
    }

    public function addProperty(string $name, SchemaInterface $schema, bool $overwrite = false): ContainerProperty
    {
        if (!isset($this->properties[$name])) {
            $this->properties[$name] = new ContainerProperty($name, $schema);
        } else {
            if (get_class($this->properties[$name]->getSchema()) !== get_class($schema)) {
                throw new DigitalMarketingFrameworkException(sprintf('Schema container property does not match existing property ("%s%, "%s").', get_class($this->properties[$name]->getSchema()), get_class($schema)));
            }
            if ($overwrite) {
                $this->properties[$name] = new ContainerProperty($name, $schema);
            }
        }
        return $this->properties[$name];
    }

    /**
     * @return array<string,ContainerProperty>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $name): ?ContainerProperty
    {
        return $this->properties[$name] ?? null;
    }

    public function removeProperty(string $name): void
    {
        if (isset($this->properties[$name])) {
            unset($this->properties[$name]);
        }
    }

    public function getValueSets(): array
    {
        $valueSets = parent::getValueSets();
        foreach ($this->properties as $property) {
            $valueSets = $this->mergeValueSets($valueSets, $property->getSchema()->getValueSets());
        }
        return $valueSets;
    }

    protected function getConfig(): array
    {
        $properties = [];
        foreach ($this->properties as $property) {
            if (SchemaDocument::FLATTEN_SCHEMA) {
                $properties[] = ['key' => $property->getName()]
                    + $property->getSchema()->toArray()
                    + ($property->getRenderingDefinition()->toArray() ?? []);
            } else {
                $properties[] = [
                    'key' => $property->getName(),
                    'value' => $property->getSchema()->toArray(),
                    'render' => $property->getRenderingDefinition()->toArray(),
                ];
            }
        }
        return parent::getConfig() + [
            'values' => $properties,
        ];
    }

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed
    {
        $defaultValue = parent::getDefaultValue($schemaDocument);
        if ($defaultValue !== null) {
            return $defaultValue;
        }
        $result = [];
        foreach ($this->getProperties() as $property) {
            $result[$property->getName()] = $property->getSchema()->getDefaultValue($schemaDocument);
        }
        return $result;
    }

    public function preSaveDataTransform(mixed &$value, SchemaDocument $schemaDocument): void
    {
        if (!is_array($value) || empty($value)) {
            $value = (object)[];
        } else {
            foreach (array_keys($value) as $key) {
                $this->getProperty($key)->getSchema()->preSaveDataTransform($value[$key], $schemaDocument);
            }
        }
    }
}
