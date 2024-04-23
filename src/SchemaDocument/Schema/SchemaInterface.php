<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\SchemaDocument\Condition\Condition;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
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

    public function setRequired(string $message = 'Required Field'): void;

    /**
     * This method is oddly named, which is because its purpose is odd too.
     * Unfortunately, some configuration document producers need to adjust
     * the PHP data according to its schema before they can perform the document production.
     *
     * For example, the empty PHP array [] can be interpreted as an empty list and an empty
     * associative object, which is expressed differently in both YAML and JSON: "{}" vs "[]"
     * That is why we need to read the schema to be able to tell, which empty array is supposed
     * to become what kind of value in the produced document.
     */
    public function preSaveDataTransform(mixed &$value, SchemaDocument $schemaDocument): void;
}
