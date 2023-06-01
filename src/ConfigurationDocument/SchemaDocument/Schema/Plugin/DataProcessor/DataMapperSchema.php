<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class DataMapperSchema extends ContainerSchema
{
    public const TYPE = 'DATA_MAPPER';

    public function addItem(string $keyword, SchemaInterface $schema): void
    {
        $property = $this->addProperty($keyword, $schema);
        $property->getRenderingDefinition()->setVisibilityConditionByToggle('./enabled');
        $this->getRenderingDefinition()->setNavigationItem(false);
    }
}
