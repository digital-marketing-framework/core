<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\AjaxController;

use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\Response;

/**
 * Full document - a configuration document that is always saved as a whole.
 * There is no inheritance, except for the defaults.
 */
abstract class FullDocumentConfigurationEditorAjaxController extends ConfigurationEditorAjaxController
{
    protected function mergeConfiguration(array $configuration, bool $inheritedConfigurationOnly = false): array
    {
        return $inheritedConfigurationOnly ? $this->getDefaultConfiguration() : $configuration;
    }

    protected function splitConfiguration(array $mergedConfiguration): array
    {
        return $mergedConfiguration;
    }

    protected function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array
    {
        throw new BadMethodCallException('This configuration controller does not support includes.');
    }
}
