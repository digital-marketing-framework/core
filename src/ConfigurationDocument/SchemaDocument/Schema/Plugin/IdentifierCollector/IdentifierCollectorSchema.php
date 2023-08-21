<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\IdentifierCollector;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class IdentifierCollectorSchema extends ContainerSchema
{
    public function addItem(string $keyword, SchemaInterface $schema): void
    {
        $this->addValueToValueSet('identifierCollector/all', $keyword);
        $this->addProperty($keyword, $schema);
    }
}
