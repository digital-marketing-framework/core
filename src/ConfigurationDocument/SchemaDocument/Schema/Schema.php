<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition\Condition;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition\NotEmptyCondition;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinition;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value\ValueSet;

abstract class Schema implements SchemaInterface
{
    protected RenderingDefinitionInterface $renderingDefinition;

    /** @var array<string,ValueSet> $valueSets */
    protected array $valueSets = [];

    /** @var array<array{condition:Condition,message:string}> $strictValidations */
    protected array $strictValidations = [];

    /** @var array<array{condition:Condition,message:string}> $validations */
    protected array $validations = [];

    public function __construct(
        protected mixed $defaultValue = null,
    ) {
        $this->renderingDefinition = new RenderingDefinition();
    }

    public function getRenderingDefinition(): RenderingDefinitionInterface
    {
        return $this->renderingDefinition;
    }

    public function addValueToValueSet(string $name, string|int|bool $value, ?string $label = null): void
    {
        if (!isset($this->valueSets[$name])) {
            $this->valueSets[$name] = new ValueSet();
        }
        $this->valueSets[$name]->addValue($value, $label);
    }

    /**
     * @param array<string,ValueSet> $a
     * @param array<string,ValueSet> $b
     * @param array<string,ValueSet>
     */
    protected function mergeValueSets(array $a, array $b): array
    {
        $mergedSets = [];
        foreach ($a as $valueSetName => $valueSet) {
            $mergedSet = $mergedSets[$valueSetName] ?? new ValueSet();
            $mergedSet->merge($valueSet);
            $mergedSets[$valueSetName] = $mergedSet;
        }
        foreach ($b as $valueSetName => $valueSet) {
            $mergedSet = $mergedSets[$valueSetName] ?? new ValueSet();
            $mergedSet->merge($valueSet);
            $mergedSets[$valueSetName] = $mergedSet;
        }
        return $mergedSets;
    }

    /**
     * @return array<string,ValueSet>
     */
    public function getValueSets(): array
    {
        return $this->valueSets;
    }

    abstract public function getType(): string;

    protected function getConfig(): array
    {
        $config = [
            'default' => $this->defaultValue,
        ];
        if (count($this->strictValidations) > 0) {
            $config['strictValidations'] = array_map(function(array $evaluation) {
                return [
                    'condition' => $evaluation['condition']->toArray(),
                    'message' => $evaluation['message'],
                ];
            }, $this->strictValidations);
        }
        if (count($this->validations) > 0) {
            $config['validations'] = array_map(function(array $evaluation) {
                return [
                    'condition' => $evaluation['condition']->toArray(),
                    'message' => $evaluation['message'],
                ];
            }, $this->validations);
        }
        return $config;
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

    public function setRequired(string $message = 'Required Field'): void
    {
        // required fields should only be enforced in final documents, not in parent documents
        $this->addSoftValidation(new NotEmptyCondition(), $message);
    }

    public function addValidation(Condition $condition, string $message, bool $strict = true): void
    {
        $validation = [
            'condition' => $condition,
            'message' => $message,
        ];
        if ($strict) {
            $this->strictValidations[] = $validation;
        } else {
            $this->validations[] = $validation;
        }
    }

    public function addStrictValidation(Condition $condition, string $message): void
    {
        $this->addValidation($condition, $message, true);
    }

    public function addSoftValidation(Condition $condition, string $message): void
    {
        $this->addValidation($condition, $message, false);
    }

    public function preSaveDataTransform(mixed &$value, SchemaDocument $schemaDocument): void
    {
    }
}
