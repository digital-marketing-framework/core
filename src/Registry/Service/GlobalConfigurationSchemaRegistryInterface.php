<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\GlobalConfigurationSchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface GlobalConfigurationSchemaRegistryInterface
{
    public function addGlobalConfigurationSchemaForPackage(string $packageName, GlobalConfigurationSchemaInterface $schema): void;

    public function addGlobalConfigurationSchemaDocument(SchemaDocument $globalConfigurationSchemaDocument): void;

    public function getGlobalConfigurationSchemaDocument(): SchemaDocument;
}
