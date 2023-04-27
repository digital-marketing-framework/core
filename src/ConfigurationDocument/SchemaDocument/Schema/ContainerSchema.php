<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

class ContainerSchema extends Schema
{
    /**
     * @var array<string,ContainerProperty>
     */
    protected array $properties = [];

    protected function getType(): string
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

    protected function getConfig(): ?array
    {
        $properties = [];
        foreach ($this->properties as $property) {
            $properties[] = [
                'itemName' => $property->getName(),
                'itemSchema' => $property->getSchema()->toArray(),
                'render' => $property->getRenderingDefinition()->toArray(),
            ];
        }
        return [
            'items' => $properties,
        ];
    }
}
