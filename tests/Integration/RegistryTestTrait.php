<?php

namespace DigitalMarketingFramework\Core\Tests\Integration;

use DigitalMarketingFramework\Core\Log\LoggerFactoryInterface;
use DigitalMarketingFramework\Core\ConfigurationResolverInitialization;
use DigitalMarketingFramework\Core\Registry\ConfigurationResolverRegistry;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;

trait RegistryTestTrait // extends \PHPUnit\Framework\TestCase
{
    protected LoggerFactoryInterface $loggerFactory;

    protected ConfigurationResolverRegistryInterface $registry;

    protected function initRegistry()
    {
        // mock everything from the outside world
        $this->loggerFactory = $this->createMock(LoggerFactoryInterface::class);
        
        $this->registry = new ConfigurationResolverRegistry($this->loggerFactory);
        ConfigurationResolverInitialization::initialize($this->registry);
    }
}
