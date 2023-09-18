<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Registry\Plugin\DataProcessorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationDocumentManagerRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationSchemaRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\FileStorageRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\TemplateEngineRegistryInterface;

interface RegistryInterface extends
    GlobalConfigurationRegistryInterface,

    LoggerFactoryRegistryInterface,
    ContextRegistryInterface,
    CacheRegistryInterface,
    ConfigurationSchemaRegistryInterface,
    ConfigurationDocumentManagerRegistryInterface,
    FileStorageRegistryInterface,
    TemplateEngineRegistryInterface,

    DataProcessorRegistryInterface,
    IdentifierCollectorRegistryInterface
{
    /**
     * Create a class instance and process awareness
     *
     * @param array<mixed> $arguments
     */
    public function createObject(string $class, array $arguments = []): object;
}
