<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Controller;

use BadMethodCallException;

/**
 * The controller will always produce the full configuration. No parent documents included, no delta calculated.
 */
class FullDocumentConfigurationEditorController extends AbstractConfigurationEditorController
{
    public function splitConfiguration(array $mergedConfiguration): array
    {
        return $mergedConfiguration;
    }

    public function mergeConfiguration(array $configuration, bool $inheritedConfigurationOnly = false): array
    {
        return $inheritedConfigurationOnly ? $this->getDefaultConfiguration() : $configuration;
    }

    public function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array
    {
        throw new BadMethodCallException('This configuration controller does not support includes.');
    }
}
