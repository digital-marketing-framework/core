<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManager;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;
use DigitalMarketingFramework\Core\Registry\RegistryException;

trait ConfigurationDocumentManagerRegistryTrait
{
    protected ConfigurationDocumentStorageInterface $configurationDocumentStorage;

    protected ConfigurationDocumentParserInterface $configurationDocumentParser;

    protected ?ConfigurationDocumentStorageInterface $staticConfigurationDocumentStorage = null;

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
        $this->configurationDocumentStorage->initalizeConfigurationDocumentStorage();
    }

    public function getStaticConfigurationDocumentStorage(): ?ConfigurationDocumentStorageInterface
    {
        return $this->staticConfigurationDocumentStorage;
    }

    public function setStaticConfigurationDocumentStorage(?ConfigurationDocumentStorageInterface $staticConfigurationDocumentStorage): void
    {
        $this->staticConfigurationDocumentStorage = $staticConfigurationDocumentStorage;
        $this->staticConfigurationDocumentStorage?->initalizeConfigurationDocumentStorage();
    }

    public function getConfigurationDocumentParser(): ConfigurationDocumentParserInterface
    {
        if (!isset($this->configurationDocumentParser)) {
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
            $staticConfigurationDocumentStorage = $this->getStaticConfigurationDocumentStorage();
            /** @var ConfigurationDocumentManager */
            $configurationDocumentManager = $this->createObject(
                ConfigurationDocumentManager::class,
                [
                    $configurationDocumentStorage,
                    $configurationDocumentParser,
                    $staticConfigurationDocumentStorage,
                ]
            );
            $this->configurationDocumentManager = $configurationDocumentManager;
        }

        return $this->configurationDocumentManager;
    }

    public function setConfigurationDocumentManager(ConfigurationDocumentManagerInterface $configurationDocumentManager): void
    {
        $this->configurationDocumentManager = $configurationDocumentManager;
        $this->setStaticConfigurationDocumentStorage($configurationDocumentManager->getStaticStorage());
        $this->setConfigurationDocumentStorage($configurationDocumentManager->getStorage());
        $this->setConfigurationDocumentParser($configurationDocumentManager->getParser());
    }

    public function addSchemaMigration(ConfigurationDocumentMigrationInterface $migration): void
    {
        $this->getConfigurationDocumentManager()->addMigration($migration);
    }
}
