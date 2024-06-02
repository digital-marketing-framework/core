<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerAwareTrait;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;

/**
 * The controller will produce a delta configuration, based on the default configuration and included parent documents.
 */
class InheritedDocumentConfigurationEditorController extends AbstractConfigurationEditorController implements ConfigurationDocumentManagerAwareInterface
{
    use ConfigurationDocumentManagerAwareTrait;

    public function splitConfiguration(array $mergedConfiguration): array
    {
        $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromConfiguration($mergedConfiguration);
        array_pop($configurationStack);
        $parentConfiguration = ConfigurationUtility::mergeConfigurationStack($configurationStack);

        return ConfigurationUtility::splitConfiguration($parentConfiguration, $mergedConfiguration);
    }

    public function mergeConfiguration(array $configuration, bool $inheritedConfigurationOnly = false): array
    {
        $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromConfiguration($configuration);

        if ($inheritedConfigurationOnly) {
            array_pop($configurationStack);
        }

        return ConfigurationUtility::mergeConfigurationStack($configurationStack);
    }

    public function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array
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
