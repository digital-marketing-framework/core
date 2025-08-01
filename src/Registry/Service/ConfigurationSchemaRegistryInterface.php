<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface ConfigurationSchemaRegistryInterface
{
    public function getActiveFieldContext(): string;

    public function setActiveFieldContext(string $fieldContext): void;

    public function addConfigurationSchemaDocumentEditorContext(SchemaDocument $schemaDocument): void;

    public function addConfigurationSchemaDocument(SchemaDocument $schemaDocument): void;

    public function getConfigurationSchemaDocument(): SchemaDocument;

    public function addSchemaVersion(string $key, string $version): void;
}
