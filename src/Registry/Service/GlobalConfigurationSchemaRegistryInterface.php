<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface GlobalConfigurationSchemaRegistryInterface
{
    public function addGlobalConfigurationSchemaForPackage(string $packageName, SchemaInterface $schema): void;

    public function addGlobalConfigurationSchemaDocument(SchemaDocument $globalConfigurationSchemaDocument): void;

    public function getGlobalConfigurationSchemaDocument(): SchemaDocument;
}
