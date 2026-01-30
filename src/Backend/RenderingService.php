<?php

namespace DigitalMarketingFramework\Core\Backend;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareTrait;
use DigitalMarketingFramework\Core\ConfigurationEditor\MetaData;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\CoreSettings;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class RenderingService implements RenderingServiceInterface, GlobalConfigurationAwareInterface, ConfigurationDocumentManagerAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use ConfigurationDocumentManagerAwareTrait;

    protected UriBuilderInterface $uriBuilder;

    public function __construct(RegistryInterface $registry)
    {
        $this->uriBuilder = $registry->getBackendUriBuilder();
    }

    public function getTextAreaDataAttributes(
        bool $ready,
        string $mode,
        bool $readonly,
        bool $globalDocument,
        string $documentType = MetaData::DEFAULT_DOCUMENT_TYPE,
        bool $includes = true,
        array $parameters = [],
        string $contextIdentifier = '',
        string $uid = '',
        string $documentName = '',
        string $contextType = '',
    ): array {
        $debug = $this->globalConfiguration->getGlobalSettings(CoreSettings::class)->debug();
        $parameters['documentType'] ??= $documentType;
        $dataAttributes = [
            'app' => $ready ? 'true' : 'false',
            'mode' => $mode,
            'readonly' => $readonly ? 'true' : 'false',
            'global-document' => $globalDocument ? 'true' : 'false',
            'debug' => $debug ? 'true' : 'false',
            'uid' => $uid,
            'context-identifier' => $contextIdentifier,
            'context-type' => $contextType,
            'document-type' => $documentType,
            'document-group' => $parameters['contentModifierGroup'] ?? '',

            'url-schema' => $this->uriBuilder->build('ajax.configuration-editor.schema', $parameters),
            'url-defaults' => $this->uriBuilder->build('ajax.configuration-editor.defaults', $parameters),
            'url-merge' => $this->uriBuilder->build('ajax.configuration-editor.merge', $parameters),
            'url-split' => $this->uriBuilder->build('ajax.configuration-editor.split', $parameters),
        ];

        if ($includes) {
            $dataAttributes['url-update-includes'] = $this->uriBuilder->build('ajax.configuration-editor.update-includes', $parameters);
        }

        // For embedded configuration documents, provide default document info for auto-initialization
        // Only applies to configuration documents (not content modifier settings, etc.)
        if (!$globalDocument && $documentType === MetaData::DEFAULT_DOCUMENT_TYPE) {
            $defaultDocument = $this->configurationDocumentManager->getDefaultConfigurationIdentifier();
            if ($defaultDocument !== '') {
                $dataAttributes['default-document'] = $defaultDocument;
            }

            if ($documentName !== '') {
                $dataAttributes['document-name'] = $documentName;
            }
        }

        return $dataAttributes;
    }
}
