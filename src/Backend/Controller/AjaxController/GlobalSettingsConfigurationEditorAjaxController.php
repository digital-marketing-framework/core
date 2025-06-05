<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\AjaxController;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class GlobalSettingsConfigurationEditorAjaxController extends FullDocumentConfigurationEditorAjaxController
{
    protected SchemaDocument $schemaDocument;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            'global-settings'
        );

        $this->schemaDocument = $this->registry->getRegistryCollection()->getGlobalConfigurationSchemaDocument();
    }

    protected function getSchemaDocument(): SchemaDocument
    {
        return $this->schemaDocument;
    }
}
