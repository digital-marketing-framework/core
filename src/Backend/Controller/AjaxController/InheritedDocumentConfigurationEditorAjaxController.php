<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\AjaxController;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareTrait;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;

/**
 * Inherited document - a configuration document that only saves the values that differ from their defaults and parent documents.
 * Inheritance is possible, not just defaults.
 */
abstract class InheritedDocumentConfigurationEditorAjaxController extends ConfigurationEditorAjaxController implements ConfigurationDocumentManagerAwareInterface
{
    use ConfigurationDocumentManagerAwareTrait;

    protected function mergeConfiguration(array $configuration, bool $inheritedConfigurationOnly = false): array
    {
        $schemaDocument = $this->getSchemaDocument();
        $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromConfiguration($configuration, $schemaDocument);

        if ($inheritedConfigurationOnly) {
            array_pop($configurationStack);
        }

        return ConfigurationUtility::mergeConfigurationStack($configurationStack);
    }

    protected function splitConfiguration(array $mergedConfiguration): array
    {
        $schemaDocument = $this->getSchemaDocument();
        $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromConfiguration($mergedConfiguration, $schemaDocument);
        array_pop($configurationStack);
        $parentConfiguration = ConfigurationUtility::mergeConfigurationStack($configurationStack);

        return ConfigurationUtility::splitConfiguration($parentConfiguration, $mergedConfiguration);
    }

    protected function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array
    {
        $schemaDocument = $this->getSchemaDocument();
        $oldIncludes = $this->configurationDocumentManager->getIncludes($referenceMergedConfiguration);
        $newIncludes = $this->configurationDocumentManager->getIncludes($mergedConfiguration);
        $this->configurationDocumentManager->setIncludes($mergedConfiguration, $oldIncludes);
        $splitConfiguration = $this->splitConfiguration($mergedConfiguration);

        $this->configurationDocumentManager->setIncludes($splitConfiguration, $newIncludes);
        $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromConfiguration($splitConfiguration, $schemaDocument);
        if ($inheritedConfigurationOnly) {
            array_pop($configurationStack);
        }

        return ConfigurationUtility::mergeConfigurationStack($configurationStack);
    }
}
