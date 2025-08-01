<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\AjaxController;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class ConfigurationDocumentConfigurationEditorAjaxController extends InheritedDocumentConfigurationEditorAjaxController
{
    protected SchemaDocument $schemaDocument;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            'configuration-document'
        );

        $this->schemaDocument = $this->registry->getRegistryCollection()->getConfigurationSchemaDocument(true);
    }

    protected function getSchemaDocument(): SchemaDocument
    {
        return $this->schemaDocument;
    }
}
