<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Registry\Plugin\DataProcessorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\SchemaProcessorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ApiRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\AssetServiceRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationDocumentManagerRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationSchemaRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\FileStorageRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationSchemaRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ResourceServiceRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ScriptAssetsRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\StaticConfigurationDocumentRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\TemplateEngineRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\TemplateRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\VendorResourceServiceRegistryInterface;

interface RegistryInterface extends
    GlobalConfigurationRegistryInterface,
    ScriptAssetsRegistryInterface,
    GlobalConfigurationSchemaRegistryInterface,
    ResourceServiceRegistryInterface,
    TemplateRegistryInterface,

    LoggerFactoryRegistryInterface,
    ContextRegistryInterface,
    CacheRegistryInterface,
    ConfigurationSchemaRegistryInterface,
    ConfigurationDocumentManagerRegistryInterface,
    FileStorageRegistryInterface,

    AssetServiceRegistryInterface,
    TemplateEngineRegistryInterface,
    VendorResourceServiceRegistryInterface,
    StaticConfigurationDocumentRegistryInterface,

    SchemaProcessorRegistryInterface,
    DataProcessorRegistryInterface,
    IdentifierCollectorRegistryInterface,

    ApiRegistryInterface
{
    public function getRegistryCollection(): RegistryCollectionInterface;

    public function setRegistryCollection(RegistryCollectionInterface $registryCollection): void;

    /**
     * @template ClassName of object
     *
     * @param class-string<ClassName> $class
     * @param array<mixed> $arguments
     *
     * @return ClassName
     */
    public function createObject(string $class, array $arguments = []): object;

    public function processObjectAwareness(object $object): void;
}
