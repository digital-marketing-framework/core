<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition\Condition;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value\ValueSet;

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

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed;

    public function setDefaultValue(mixed $value): void;

    public function addValidation(Condition $condition, string $message, bool $strict = true): void;

    public function addStrictValidation(Condition $condition, string $message): void;

    public function addSoftValidation(Condition $condition, string $message): void;

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
