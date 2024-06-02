<?php

namespace DigitalMarketingFramework\Core;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\GlobalConfigurationSchemaInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

interface InitializationInterface
{
    public function getPackageAlias(): string;

    public function getFullPackageName(): string;

    public function initPlugins(string $domain, RegistryInterface $registry): void;

    public function initServices(string $domain, RegistryInterface $registry): void;

    public function initGlobalConfiguration(string $domain, RegistryInterface $registry): void;

    public function initMetaData(RegistryInterface $registry): void;

    public function getGlobalConfigurationSchema(): ?GlobalConfigurationSchemaInterface;

    public function setGlobalConfigurationSchema(?GlobalConfigurationSchemaInterface $globalConfigurationSchema): void;
}
