<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareTrait;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class ConfigurationDocumentSectionController extends SectionController implements ConfigurationDocumentManagerAwareInterface
{
    use ConfigurationDocumentManagerAwareTrait;

    protected SchemaDocument $schemaDocument;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            'configuration-document',
            ['list', 'edit', 'save', 'create', 'delete']
        );

        $this->schemaDocument = $registry->getRegistryCollection()->getConfigurationSchemaDocument();
    }

    protected function getDocumentIdentifier(): string
    {
        return $this->getParameters()['documentIdentifier'] ?? '';
    }

    protected function getDocumentName(): string
    {
        return $this->getParameters()['documentName'] ?? '';
    }

    protected function getDocument(): string
    {
        return $this->getParameters()['document'] ?? '';
    }

    protected function listAction(): Response
    {
        $list = [];
        $documentIdentifiers = $this->configurationDocumentManager->getDocumentIdentifiers();
        foreach ($documentIdentifiers as $documentIdentifier) {
            $list[$documentIdentifier] = $this->configurationDocumentManager->getDocumentInformation($documentIdentifier);
        }

        $this->viewData['documents'] = $list;

        return $this->render();
    }

    protected function editAction(): Response
    {
        $this->addConfigurationEditorAssets();
        $documentIdentifier = $this->getDocumentIdentifier();

        $document = $this->configurationDocumentManager->getDocumentInformation($documentIdentifier);
        $document['content'] = $this->configurationDocumentManager->getDocumentFromIdentifier($documentIdentifier);
        $this->viewData['document'] = $document;

        return $this->render();
    }

    protected function saveAction(): Response
    {
        $documentIdentifier = $this->getDocumentIdentifier();
        $document = $this->getDocument();
        $this->configurationDocumentManager->saveDocument($documentIdentifier, $document, $this->schemaDocument);

        return $this->redirect('page.configuration-document.edit', ['documentIdentifier' => $documentIdentifier]);
    }

    protected function createAction(): Response
    {
        $documentName = $this->getDocumentName();
        $documentIdentifier = $this->configurationDocumentManager->getDocumentIdentifierFromBaseName($documentName);
        $this->configurationDocumentManager->createDocument($documentIdentifier, '', $documentName, $this->schemaDocument);

        return $this->redirect('page.configuration-document.edit', ['documentIdentifier' => $documentIdentifier]);
    }

    protected function deleteAction(): Response
    {
        $documentIdentifier = $this->getDocumentIdentifier();
        $this->configurationDocumentManager->deleteDocument($documentIdentifier);

        return $this->redirect('page.configuration-document.list');
    }
}
