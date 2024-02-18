<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Controller;

use BadMethodCallException;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;

/**
 * The controller will produce a delta configuration, based on the default configuration. No parent documents included.
 */
class DeltaDocumentConfigurationEditorController extends AbstractConfigurationEditorController
{
    public function splitConfiguration(array $mergedConfiguration): array
    {
        return ConfigurationUtility::splitConfiguration(
            $this->getDefaultConfiguration(),
            $mergedConfiguration
        );
    }

    public function mergeConfiguration(array $configuration, bool $inheritedConfigurationOnly = false): array
    {
        $defaultConfiguration = $this->getDefaultConfiguration();

        if ($inheritedConfigurationOnly) {
            return $defaultConfiguration;
        }

        $configurationStack = [
            $defaultConfiguration,
            $configuration,
        ];

        return ConfigurationUtility::mergeConfigurationStack($configurationStack);
    }

    public function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array
    {
        throw new BadMethodCallException('This configuration controller does not support includes.');
    }
}
