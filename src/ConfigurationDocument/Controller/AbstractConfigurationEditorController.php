<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

abstract class AbstractConfigurationEditorController implements ConfigurationEditorControllerInterface
{
    public function __construct(
        protected SchemaDocument $schemaDocument,
    ) {
    }

    public function getDefaultConfiguration(): array
    {
        return $this->getSchemaDocument()->getDefaultValue();
    }

    public function getSchemaDocument(): SchemaDocument
    {
        return $this->schemaDocument;
    }

    public function getSchemaDocumentAsArray(): array
    {
        return $this->getSchemaDocument()->toArray();
    }
}
