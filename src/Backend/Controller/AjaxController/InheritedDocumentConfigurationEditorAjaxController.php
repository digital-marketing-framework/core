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
        $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromConfiguration($configuration);

        if ($inheritedConfigurationOnly) {
            array_pop($configurationStack);
        }

        return ConfigurationUtility::mergeConfigurationStack($configurationStack);
    }

    protected function splitConfiguration(array $mergedConfiguration): array
    {
        $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromConfiguration($mergedConfiguration);
        array_pop($configurationStack);
        $parentConfiguration = ConfigurationUtility::mergeConfigurationStack($configurationStack);

        return ConfigurationUtility::splitConfiguration($parentConfiguration, $mergedConfiguration);
    }

    protected function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array
    {
        $oldIncludes = $this->configurationDocumentManager->getIncludes($referenceMergedConfiguration);
        $newIncludes = $this->configurationDocumentManager->getIncludes($mergedConfiguration);
        $this->configurationDocumentManager->setIncludes($mergedConfiguration, $oldIncludes);
        $splitConfiguration = $this->splitConfiguration($mergedConfiguration);

        $this->configurationDocumentManager->setIncludes($splitConfiguration, $newIncludes);
        $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromConfiguration($splitConfiguration);
        if ($inheritedConfigurationOnly) {
            array_pop($configurationStack);
        }

        return ConfigurationUtility::mergeConfigurationStack($configurationStack);
    }
}
