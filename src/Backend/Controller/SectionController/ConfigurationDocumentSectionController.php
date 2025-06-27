<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\CoreSettings;
use DigitalMarketingFramework\Core\Model\ConfigurationDocument\ConfigurationDocumentInformation;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

/**
 * @extends ListSectionController<ConfigurationDocumentInformation>
 */
class ConfigurationDocumentSectionController extends ListSectionController implements GlobalConfigurationAwareInterface, ConfigurationDocumentManagerAwareInterface
{
    use GlobalConfigurationAwareTrait;
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

    protected function getDocumentName(): string
    {
        return $this->getParameters()['name'] ?? '';
    }

    protected function getDocument(): string
    {
        return $this->getParameters()['document'] ?? '';
    }

    protected function getDocumentIdentifier(): string
    {
        return $this->getParameters()['documentIdentifier'] ?? '';
    }

    protected function fetchFilteredCount(array $filters): int
    {
        // TODO no filtering yet
        return count($this->configurationDocumentManager->getDocumentIdentifiers());
    }

    protected function fetchFiltered(array $filters, array $navigation): array
    {
        // TODO no pagination or filtering yet
        $list = [];
        $documentIdentifiers = $this->configurationDocumentManager->getDocumentIdentifiers();
        foreach ($documentIdentifiers as $documentIdentifier) {
            $list[] = $this->configurationDocumentManager->getDocumentInformation($documentIdentifier);
        }

        return $list;
    }

    protected function editAction(): Response
    {
        $this->addConfigurationEditorAssets();
        $documentIdentifier = $this->getIdentifier();

        $document = $this->configurationDocumentManager->getDocumentInformation($documentIdentifier);
        $document->setContent($this->configurationDocumentManager->getDocumentFromIdentifier($documentIdentifier));
        $this->viewData['document'] = $document;

        $this->viewData['debug'] = $this->globalConfiguration->getGlobalSettings(CoreSettings::class)->debug();

        return $this->render();
    }

    protected function saveAction(): Response
    {
        $documentIdentifier = $this->getDocumentIdentifier();
        $document = $this->getDocument();
        $this->configurationDocumentManager->saveDocument($documentIdentifier, $document, $this->schemaDocument);

        return $this->redirect('page.configuration-document.edit', ['id' => $documentIdentifier]);
    }

    protected function createAction(): Response
    {
        $documentName = $this->getDocumentName();
        $documentIdentifier = $this->configurationDocumentManager->getDocumentIdentifierFromBaseName($documentName);
        $this->configurationDocumentManager->createDocument($documentIdentifier, '', $documentName, $this->schemaDocument);

        return $this->redirect('page.configuration-document.edit', ['id' => $documentIdentifier]);
    }

    protected function deleteAction(): Response
    {
        $ids = $this->getSelectedItems();
        foreach ($ids as $id) {
            $this->configurationDocumentManager->deleteDocument($id);
        }

        return $this->redirect('page.configuration-document.list');
    }
}
