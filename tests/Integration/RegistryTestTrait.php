<?php

namespace DigitalMarketingFramework\Core\Tests\Integration;

use DigitalMarketingFramework\Core\Log\LoggerFactoryInterface;
use DigitalMarketingFramework\Core\ConfigurationResolverInitialization;
use DigitalMarketingFramework\Core\CorePluginInitialization;
use DigitalMarketingFramework\Core\Log\LoggerInterface;
use DigitalMarketingFramework\Core\Registry\ConfigurationResolverRegistry;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;
use PHPUnit\Framework\MockObject\MockObject;

trait RegistryTestTrait // extends \PHPUnit\Framework\TestCase
{
    protected LoggerFactoryInterface $loggerFactory;

    protected LoggerInterface&MockObject $logger;

    protected ConfigurationResolverRegistryInterface $registry;

    protected function initRegistry(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->loggerFactory = $this->createMock(LoggerFactoryInterface::class);
        $this->loggerFactory->method('getLogger')->willReturn($this->logger);
        
        $this->registry = new ConfigurationResolverRegistry($this->loggerFactory);
        CorePluginInitialization::initialize($this->registry);
    }
}
