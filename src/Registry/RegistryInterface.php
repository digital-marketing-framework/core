<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationDocumentManagerRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryInterface;

interface RegistryInterface extends
    GlobalConfigurationRegistryInterface,

    LoggerFactoryRegistryInterface,
    ContextRegistryInterface,
    CacheRegistryInterface,
    ConfigurationDocumentManagerRegistryInterface,

    ConfigurationResolverRegistryInterface,
    IdentifierCollectorRegistryInterface
{
    /**
     * Create a class instance and process awareness
     */
    public function createObject(string $class, array $arguments = []): object;

    public function getDefaultConfiguration(): array;
    public function getConfigurationSchema(): array;
}
