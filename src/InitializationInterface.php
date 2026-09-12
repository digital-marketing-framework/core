<?php

namespace DigitalMarketingFramework\Core;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\GlobalConfigurationSchemaInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

interface InitializationInterface
{
    public function getPackageAlias(): string;

    public function getFullPackageName(): string;

    public function initPlugins(string $domain, RegistryInterface $registry): void;

    /**
     * Registers services on the registry.
     *
     * Plugins are not registered yet at this point, and the schema processors are plugins, so
     * anything here that needs global configuration must read it raw through
     * GlobalConfigurationInterface::get() rather than resolving a settings object.
     */
    public function initServices(string $domain, RegistryInterface $registry): void;

    public function initGlobalConfiguration(string $domain, RegistryInterface $registry): void;

    public function initMetaData(RegistryInterface $registry): void;

    public function getGlobalConfigurationSchema(): ?GlobalConfigurationSchemaInterface;

    public function setGlobalConfigurationSchema(?GlobalConfigurationSchemaInterface $globalConfigurationSchema): void;
}
