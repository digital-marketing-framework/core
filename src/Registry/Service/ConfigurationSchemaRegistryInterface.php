<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

interface ConfigurationSchemaRegistryInterface
{
    public function addConfigurationSchema(SchemaDocument $schemaDocument): void;

    public function getConfigurationSchema(): SchemaDocument;

    public function addSchemaVersion(string $key, string $version): void;
}
