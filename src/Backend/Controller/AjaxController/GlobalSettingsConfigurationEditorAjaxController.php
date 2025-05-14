<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\AjaxController;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class GlobalSettingsConfigurationEditorAjaxController extends FullDocumentConfigurationEditorAjaxController
{
    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            $registry->getRegistryCollection()->getGlobalConfigurationSchemaDocument(),
            'global-settings'
        );
    }
}
