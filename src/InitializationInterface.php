<?php

namespace DigitalMarketingFramework\Core;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;

interface InitializationInterface
{
    public function initPlugins(string $domain, RegistryInterface $registry): void;

    public function initServices(string $domain, RegistryInterface $registry): void;

    public function initGlobalConfiguration(string $domain, RegistryInterface $registry): void;

    public function initMetaData(RegistryInterface $registry): void;
}
