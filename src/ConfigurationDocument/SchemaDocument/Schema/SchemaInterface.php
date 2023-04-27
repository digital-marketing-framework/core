<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;

interface SchemaInterface
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array;

    public function getRenderingDefinition(): RenderingDefinitionInterface;

    /**
     * @return array<string<array<string>>
     */
    public function getValueSets(): array;

    public function getCustomType(): ?string;
}
