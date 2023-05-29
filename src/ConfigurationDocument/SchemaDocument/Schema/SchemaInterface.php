<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

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

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed;
    public function setDefaultValue(mixed $value): void;
}
