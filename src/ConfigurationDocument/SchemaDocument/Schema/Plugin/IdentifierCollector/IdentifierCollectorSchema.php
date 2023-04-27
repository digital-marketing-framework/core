<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\IdentifierCollector;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\PluginSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\IdentifierCollector\IdentifierCollectorInterface;

class IdentifierCollectorSchema extends PluginSchema
{
    public function addIdentifierCollector(string $keyword, SchemaInterface $schema): void
    {
        $this->addProperty($keyword, $schema);
    }

    protected function getPluginInterface(): string
    {
        return IdentifierCollectorInterface::class;
    }

    protected function processPlugin(string $keyword, string $class): void
    {
        $this->addIdentifierCollector($keyword, $class::getSchema());
    }
}
