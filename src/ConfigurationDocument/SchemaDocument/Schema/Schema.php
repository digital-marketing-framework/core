<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinition;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

abstract class Schema implements SchemaInterface
{
    protected RenderingDefinitionInterface $renderingDefinition;

    /** @var array<string,array<string>> $valueSets */
    protected array $valueSets = [];

    public function __construct(
        protected mixed $defaultValue = null,
    ) {
        $this->renderingDefinition = new RenderingDefinition();
    }

    public function getRenderingDefinition(): RenderingDefinitionInterface
    {
        return $this->renderingDefinition;
    }

    protected function mergeValueSets(array $a, array $b): array
    {
        foreach ($b as $name => $set) {
            if (!isset($a[$name])) {
                $a[$name] = $set;
            } else {
                foreach ($b[$name] as $value) {
                    $a[$name] = $value;
                }
                $a[$name] = array_values(array_unique($a[$name]));
            }
        }
        return $a;
    }

    /**
     * @return array<string<array<string>>
     */
    public function getValueSets(): array
    {
        return $this->valueSets;
    }

    abstract public function getType(): string;

    protected function getConfig(): array
    {
        return [
            'default' => $this->defaultValue,
        ];
    }

    public function toArray(): array
    {
        if (SchemaDocument::FLATTEN_SCHEMA) {
            return ['type' => $this->getType()]
                + ($this->getConfig() ?? [])
                + ($this->renderingDefinition->toArray() ?? []);
        } else {
            return [
                'type' => $this->getType(),
                'config' => $this->getConfig(),
                'render' => $this->renderingDefinition->toArray(),
            ];
        }
    }

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(mixed $value): void
    {
        $this->defaultValue = $value;
    }
}
