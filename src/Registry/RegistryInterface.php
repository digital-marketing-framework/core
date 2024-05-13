<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Registry\Plugin\DataProcessorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\SchemaProcessorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ApiRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\AssetsRegistryInterface;
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
    AssetsRegistryInterface,

    LoggerFactoryRegistryInterface,
    ContextRegistryInterface,
    CacheRegistryInterface,
    ConfigurationSchemaRegistryInterface,
    ConfigurationDocumentManagerRegistryInterface,
    FileStorageRegistryInterface,
    TemplateEngineRegistryInterface,

    SchemaProcessorRegistryInterface,
    DataProcessorRegistryInterface,
    IdentifierCollectorRegistryInterface,

    ApiRegistryInterface
{
    /**
     * @template ClassName of object
     *
     * @param class-string<ClassName> $class
     * @param array<mixed> $arguments
     *
     * @return ClassName
     */
    public function createObject(string $class, array $arguments = []): object;
}
