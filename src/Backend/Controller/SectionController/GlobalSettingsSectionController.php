<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\CoreSettings;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorAwareInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorAwareTrait;

class GlobalSettingsSectionController extends SectionController implements GlobalConfigurationAwareInterface, SchemaProcessorAwareInterface, ConfigurationDocumentParserAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use SchemaProcessorAwareTrait;
    use ConfigurationDocumentParserAwareTrait;

    protected SchemaDocument $schemaDocument;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            'global-settings',
            ['edit', 'save']
        );

        $this->schemaDocument = $registry->getRegistryCollection()->getGlobalConfigurationSchemaDocument();
    }

    protected function editAction(): Response
    {
        $this->addConfigurationEditorAssets();

        $data = [];
        $properties = $this->schemaDocument->getMainSchema()->getProperties();
        foreach (array_keys($properties) as $key) {
            $data[$key] = $this->globalConfiguration->get($key) ?? [];
        }

        $this->schemaProcessor->convertValueTypes($this->schemaDocument, $data);
        $document = $this->configurationDocumentParser->produceDocument($data, $this->schemaDocument);
        $this->viewData['document'] = $document;

        $this->viewData['debug'] = $this->globalConfiguration->getGlobalSettings(CoreSettings::class)->debug();

        return $this->render();
    }

    protected function saveAction(): Response
    {
        $document = $this->request->getData()['document'] ?? '';
        $configuration = $this->configurationDocumentParser->parseDocument($document);

        foreach ($configuration as $key => $value) {
            if ($key === ConfigurationDocumentManagerInterface::KEY_META_DATA) {
                continue;
            }

            $config = $this->globalConfiguration->get($key);
            foreach ($value as $configKey => $configValue) {
                $config[$configKey] = $configValue;
            }

            $this->globalConfiguration->set($key, $config);
        }

        return $this->redirect('page.global-settings.edit');
    }
}
