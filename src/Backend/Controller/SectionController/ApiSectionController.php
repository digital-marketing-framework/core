<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareInterface;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareTrait;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareTrait;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

/**
 * @extends ListSectionController<EndPointInterface>
 */
class ApiSectionController extends ListSectionController implements EndPointStorageAwareInterface, ConfigurationDocumentManagerAwareInterface
{
    use EndPointStorageAwareTrait;
    use ConfigurationDocumentManagerAwareTrait;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            'api',
            ['list', 'edit', 'save', 'create', 'delete']
        );
    }

    protected function getItemStorage()
    {
        return $this->endPointStorage;
    }

    /**
     * Build initial configuration document for a new API endpoint.
     *
     * @return string YAML configuration document
     */
    protected function buildInitialConfigurationDocument(string $name): string
    {
        $schemaDocument = $this->registry->getConfigurationSchemaDocument();

        $metaData = [
            ConfigurationDocumentManagerInterface::KEY_DOCUMENT_NAME => 'API Endpoint: ' . ucfirst($name),
            ConfigurationDocumentManagerInterface::KEY_DOCUMENT_STRICT_VALIDATION => true,
            ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION => $schemaDocument->getVersion(),
        ];

        $defaultDocument = $this->configurationDocumentManager->getDefaultConfigurationIdentifier();
        if ($defaultDocument !== '') {
            $includeKey = 'global-configuration--configuration-document--default';
            $metaData[ConfigurationDocumentManagerInterface::KEY_INCLUDES] = [
                $includeKey => [
                    'uuid' => $includeKey,
                    'weight' => 10000,
                    'value' => $defaultDocument,
                ],
            ];
        } else {
            $metaData[ConfigurationDocumentManagerInterface::KEY_INCLUDES] = [];
        }

        $configuration = [
            ConfigurationDocumentManagerInterface::KEY_META_DATA => $metaData,
        ];

        return $this->configurationDocumentManager->getParser()->produceDocument($configuration, $schemaDocument);
    }

    protected function createAction(): Response
    {
        $name = $this->getParameters()['name'] ?? '';
        $configurationDocument = $this->buildInitialConfigurationDocument($name);

        $endPoint = $this->endPointStorage->create([
            'name' => $name,
            'configuration_document' => $configurationDocument,
        ]);
        $this->endPointStorage->add($endPoint);

        return $this->redirect('page.api.edit', [
            'id' => $endPoint->getId(),
            'returnUrl' => $this->getReturnUrl(),
        ]);
    }
}
