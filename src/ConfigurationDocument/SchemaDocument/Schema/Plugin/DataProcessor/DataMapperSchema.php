<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\PluginSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;

class DataMapperSchema extends PluginSchema
{
    public const TYPE = 'DATA_MAPPER';

    public function addDataMapper(string $keyword, SchemaInterface $schema): void
    {
        $property = $this->addProperty($keyword, $schema);
        $property->getRenderingDefinition()->setVisibilityConditionByToggle('./enabled');
    }

    protected function processPlugin(string $keyword, string $class): void
    {
        $this->addDataMapper($keyword, $class::getSchema());
    }

    protected function getPluginInterface(): string
    {
        return DataMapperInterface::class;
    }
}
