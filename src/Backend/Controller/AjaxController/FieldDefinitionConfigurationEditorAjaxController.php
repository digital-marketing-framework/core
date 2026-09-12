<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\AjaxController;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

/**
 * A field definition file is edited as a whole: it holds only what its own layer defines, and
 * what the other folders contribute is neither shown nor merged in here.
 */
class FieldDefinitionConfigurationEditorAjaxController extends FullDocumentConfigurationEditorAjaxController
{
    protected SchemaDocument $schemaDocument;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            'field-definition'
        );

        $this->schemaDocument = $registry->getFieldDefinitionManager()->getSchemaDocument();
    }

    protected function getSchemaDocument(): SchemaDocument
    {
        return $this->schemaDocument;
    }
}
