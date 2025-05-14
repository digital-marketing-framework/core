<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\AjaxController;

use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;

/**
 * Delta document - a configuration document that only saves the values that differ from their defaults.
 * There is no inheritance, except for the defaults.
 */
abstract class DeltaDocumentConfigurationEditorAjaxController extends ConfigurationEditorAjaxController
{
    protected function mergeConfiguration(array $configuration, bool $inheritedConfigurationOnly = false): array
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

    protected function splitConfiguration(array $mergedConfiguration): array
    {
        return ConfigurationUtility::splitConfiguration(
            $this->getDefaultConfiguration(),
            $mergedConfiguration
        );
    }

    protected function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array
    {
        throw new BadMethodCallException('This configuration controller does not support includes.');
    }
}
