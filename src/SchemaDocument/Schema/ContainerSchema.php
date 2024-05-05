<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class ContainerSchema extends Schema
{
    /**
     * @var array<string,ContainerProperty>
     */
    protected array $properties = [];

    public function getType(): string
    {
        return 'CONTAINER';
    }

    /**
     * Set a property regardless of whether or not a property with that name already existed.
     * Might overwrite an existing property.
     */
    public function setProperty(string $name, SchemaInterface $schema): ContainerProperty
    {
        $this->properties[$name] = new ContainerProperty($name, $schema);

        return $this->properties[$name];
    }

    /**
     * Add a property to the container. If a property with that name already exists, the schema type has to match, everything else is counted as a conflict.
     * The $overwrite flag must be set in order to overwrite an existing property.
     */
    public function addProperty(string $name, SchemaInterface $schema, bool $overwrite = false): ContainerProperty
    {
        if (!isset($this->properties[$name])) {
            $this->properties[$name] = new ContainerProperty($name, $schema);
        } else {
            if ($this->properties[$name]->getSchema()::class !== $schema::class) {
                throw new DigitalMarketingFrameworkException(sprintf('Schema container property does not match existing property ("%s%, "%s").', $this->properties[$name]->getSchema()::class, $schema::class));
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

    public function removeProperty(string $name): ?ContainerProperty
    {
        $property = null;
        if (isset($this->properties[$name])) {
            $property = $this->properties[$name];
            unset($this->properties[$name]);
        }

        return $property;
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
            if (SchemaDocument::$flattenSchema) {
                $properties[] = [
                    'key' => $property->getName(),
                    'weight' => $property->getWeight(),
                ]
                    + $property->getSchema()->toArray()
                    + ($property->getRenderingDefinition()->toArray() ?? []);
            } else {
                $properties[] = [
                    'key' => $property->getName(),
                    'weight' => $property->getWeight(),
                    'value' => $property->getSchema()->toArray(),
                    'render' => $property->getRenderingDefinition()->toArray(),
                ];
            }
        }

        return parent::getConfig() + [
            'values' => $properties,
        ];
    }
}
