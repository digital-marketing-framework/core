<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryInterface;

interface RegistryInterface extends LoggerFactoryRegistryInterface, ContextRegistryInterface, CacheRegistryInterface
{
    public function getGlobalConfiguration(): ConfigurationInterface;
    public function setGlobalConfiguration(ConfigurationInterface $globalConfiguration): void;
}
