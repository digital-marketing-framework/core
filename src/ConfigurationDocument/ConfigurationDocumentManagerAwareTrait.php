<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

trait ConfigurationDocumentManagerAwareTrait
{
    protected ConfigurationDocumentManagerInterface $configurationDocumentManager;

    public function setConfigurationDocumentManager(ConfigurationDocumentManagerInterface $configurationDocumentManager): void
    {
        $this->configurationDocumentManager = $configurationDocumentManager;
    }
}
