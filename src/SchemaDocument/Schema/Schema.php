<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\SchemaDocument\Condition\Condition;
use DigitalMarketingFramework\Core\SchemaDocument\Condition\NotEmptyCondition;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinition;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\Value\ValueSet;

abstract class Schema implements SchemaInterface
{
    protected RenderingDefinitionInterface $renderingDefinition;

    /** @var array<string,ValueSet> */
    protected array $valueSets = [];

    /** @var array<array{condition:Condition,message:string}> */
    protected array $strictValidations = [];

    /** @var array<array{condition:Condition,message:string}> */
    protected array $validations = [];

    protected bool $required = false;

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
     *
     * @return array<string,ValueSet>
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

    /**
     * @return array<string,mixed>
     */
    protected function getConfig(): array
    {
        $config = [
            'default' => $this->defaultValue,
        ];
        if ($this->required) {
            $config['required'] = true;
        }

        if ($this->strictValidations !== []) {
            $config['strictValidations'] = array_map(static fn (array $validation) => [
                'condition' => $validation['condition']->toArray(),
                'message' => $validation['message'],
            ], $this->strictValidations);
        }

        if ($this->validations !== []) {
            $config['validations'] = array_map(static fn (array $validation) => [
                'condition' => $validation['condition']->toArray(),
                'message' => $validation['message'],
            ], $this->validations);
        }

        return $config;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        if (SchemaDocument::$flattenSchema) {
            return ['type' => $this->getType()]
                + $this->getConfig()
                + ($this->renderingDefinition->toArray() ?? []);
        }

        return [
            'type' => $this->getType(),
            'config' => $this->getConfig(),
            'render' => $this->renderingDefinition->toArray(),
        ];
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(mixed $value): void
    {
        $this->defaultValue = $value;
    }

    public function setRequired(string $message = 'Required Field', bool $strict = false): void
    {
        $this->addValidation(new NotEmptyCondition(), $message, $strict);
        $this->required = true;
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
}
