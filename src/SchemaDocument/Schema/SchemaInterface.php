<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\SchemaDocument\Condition\Condition;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Value\ValueSet;

interface SchemaInterface
{
    public function getType(): string;

    /**
     * @return array<mixed>
     */
    public function toArray(): array;

    public function getRenderingDefinition(): RenderingDefinitionInterface;

    /**
     * @return array<string,ValueSet>
     */
    public function getValueSets(): array;

    public function addValueToValueSet(string $name, string|int|bool $value, ?string $label = null): void;

    public function getDefaultValue(): mixed;

    public function setDefaultValue(mixed $value): void;

    public function addValidation(Condition $condition, string $message, bool $strict = true): void;

    public function addStrictValidation(Condition $condition, string $message): void;

    public function addSoftValidation(Condition $condition, string $message): void;

    public function setRequired(string $message = 'Required Field', bool $strict = false): void;
}
