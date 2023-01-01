<?php

namespace DigitalMarketingFramework\Core\Registry;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\CacheRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\ContextRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\GlobalConfigurationRegistryInterface;
use DigitalMarketingFramework\Core\Registry\Service\LoggerFactoryRegistryInterface;

interface RegistryInterface extends 
    LoggerFactoryRegistryInterface, 
    ContextRegistryInterface, 
    CacheRegistryInterface, 
    GlobalConfigurationRegistryInterface,
    ConfigurationResolverRegistryInterface,
    IdentifierCollectorRegistryInterface
{
}
