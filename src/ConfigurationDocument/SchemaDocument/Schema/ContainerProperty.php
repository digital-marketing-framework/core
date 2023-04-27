<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinition;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;

class ContainerProperty
{
    protected RenderingDefinitionInterface $renderingDefinition;

    public function __construct(
        protected string $name, 
        protected SchemaInterface $schema
    ) {
        $this->renderingDefinition = new RenderingDefinition();
    }

    public function getRenderingDefinition(): RenderingDefinitionInterface
    {
        return $this->renderingDefinition;
    }

    public function getSchema(): SchemaInterface
    {
        return $this->schema;
    }

    public function setSchema(SchemaInterface $schema): void
    {
        $this->schema = $schema;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
