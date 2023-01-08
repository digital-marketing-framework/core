<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManager;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;
use DigitalMarketingFramework\Core\Registry\RegistryException;

trait ConfigurationDocumentManagerRegistryTrait
{
    protected ConfigurationDocumentStorageInterface $configurationDocumentStorage;
    protected ConfigurationDocumentParserInterface $configurationDocumentParser;
    protected ConfigurationDocumentManagerInterface $configurationDocumentManager;

    abstract protected function createObject(string $class, array $arguments = []): object;

    public function getConfigurationDocumentStorage(): ConfigurationDocumentStorageInterface
    {
        if (!isset($this->configurationDocumentStorage)) {
            throw new RegistryException('Configuration document storage not defined');
        }
        return $this->configurationDocumentStorage;
    }

    public function setConfigurationDocumentStorage(ConfigurationDocumentStorageInterface $configurationDocumentStorage): void
    {
        $this->configurationDocumentStorage = $configurationDocumentStorage;
    }

    public function getConfigurationDocumentParser(): ConfigurationDocumentParserInterface
    {
        if (!isset($this->configurationDocumentStorage)) {
            throw new RegistryException('Configuration document storage not defined');
        }
        return $this->configurationDocumentParser;
    }

    public function setConfigurationDocumentParser(ConfigurationDocumentParserInterface $configurationDocumentParser): void
    {
        $this->configurationDocumentParser = $configurationDocumentParser;
    }

    public function getConfigurationDocumentManager(): ConfigurationDocumentManagerInterface
    {
        if (!isset($this->configurationDocumentManager)) {
            $configurationDocumentStorage = $this->getConfigurationDocumentStorage();
            $configurationDocumentParser = $this->getConfigurationDocumentParser();
            $this->configurationDocumentManager = $this->createObject(ConfigurationDocumentManager::class, [$configurationDocumentStorage, $configurationDocumentParser]);
        }
        return $this->configurationDocumentManager;
    }

    public function setConfigurationDocumentManager(ConfigurationDocumentManagerInterface $configurationDocumentManager): void
    {
        $this->configurationDocumentManager = $configurationDocumentManager;
        $this->configurationDocumentStorage = $configurationDocumentManager->getStorage();
        $this->configurationDocumentParser = $configurationDocumentManager->getParser();
    }
}
