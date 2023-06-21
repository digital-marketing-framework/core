<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;

class DataMapperSchema extends ListSchema
{
    public const TYPE = 'DATA_MAPPER';

    protected SwitchSchema $dataMapperSchema;

    public function __construct(mixed $defaultValue = null)
    {
        $this->dataMapperSchema = new SwitchSchema('dataMapper');
        parent::__construct($this->dataMapperSchema, $defaultValue);
        $this->getRenderingDefinition()->setNavigationItem(false);
    }

    public function addItem(string $type, SchemaInterface $schema): void
    {
        $this->dataMapperSchema->addItem($type, $schema);
    }
}
