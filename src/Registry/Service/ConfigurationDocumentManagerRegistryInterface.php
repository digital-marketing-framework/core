<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;

interface ConfigurationDocumentManagerRegistryInterface
{
    public function getConfigurationDocumentStorage(): ConfigurationDocumentStorageInterface;
    public function setConfigurationDocumentStorage(ConfigurationDocumentStorageInterface $configurationDocumentStorage): void;
    
    public function getConfigurationDocumentParser(): ConfigurationDocumentParserInterface;
    public function setConfigurationDocumentParser(ConfigurationDocumentParserInterface $configurationDocumentParser): void;
    
    public function getConfigurationDocumentManager(): ConfigurationDocumentManagerInterface;
    public function setConfigurationDocumentManager(ConfigurationDocumentManagerInterface $configurationDocumentManager): void;
}
