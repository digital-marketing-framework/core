<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\AlertRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\BackendControllerRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\CleanupRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\DataProcessorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\NotificationRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\SchemaProcessorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ApiRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\AssetServiceRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\BackendTemplatingRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationDocumentManagerRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ConfigurationSchemaRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\CryptoRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\DataPrivacyManagerRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\EnvironmentRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\FileStorageRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationSchemaRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ResourceServiceRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ScriptAssetsRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\StaticConfigurationDocumentRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\TemplateEngineRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\TemplateRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\TestCaseRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\VendorResourceServiceRegistryInterface;

interface RegistryInterface extends
    GlobalConfigurationRegistryInterface,
    CryptoRegistryInterface,
    EnvironmentRegistryInterface,
    ScriptAssetsRegistryInterface,
    GlobalConfigurationSchemaRegistryInterface,
    ResourceServiceRegistryInterface,
    TemplateRegistryInterface,
    DataPrivacyManagerRegistryInterface,

    LoggerFactoryRegistryInterface,
    NotificationRegistryInterface,
    AlertRegistryInterface,
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

    ApiRegistryInterface,
    TestCaseRegistryInterface,
    BackendTemplatingRegistryInterface,
    BackendControllerRegistryInterface,
    CleanupRegistryInterface
{
    public function init(): void;

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

    public function addServiceContext(WriteableContextInterface $context): void;

    public function getHost(): string;
}
