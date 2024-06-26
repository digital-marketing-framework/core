<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

class DataMapperSchema extends ContainerSchema
{
    public const TYPE = 'DATA_MAPPER';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);
        $this->getRenderingDefinition()->setNavigationItem(false);
    }

    public function addItem(string $type, SchemaInterface $schema): void
    {
        $this->addProperty($type, $schema);
    }
}
