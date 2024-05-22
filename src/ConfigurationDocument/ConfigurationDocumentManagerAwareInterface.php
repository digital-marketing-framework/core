<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

interface ConfigurationDocumentManagerAwareInterface
{
    public function setConfigurationDocumentManager(ConfigurationDocumentManagerInterface $documentManager): void;
}
