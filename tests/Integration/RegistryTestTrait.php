<?php

namespace DigitalMarketingFramework\Core\Tests\Integration;

use DigitalMarketingFramework\Core\Log\LoggerFactoryInterface;
use DigitalMarketingFramework\Core\CorePluginInitialization;
use DigitalMarketingFramework\Core\Log\LoggerInterface;
use DigitalMarketingFramework\Core\Registry\Registry;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use PHPUnit\Framework\MockObject\MockObject;

trait RegistryTestTrait // extends \PHPUnit\Framework\TestCase
{
    protected LoggerFactoryInterface $loggerFactory;

    protected LoggerInterface&MockObject $logger;

    protected RegistryInterface $registry;

    protected function initRegistry(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->loggerFactory = $this->createMock(LoggerFactoryInterface::class);
        $this->loggerFactory->method('getLogger')->willReturn($this->logger);
        
        $this->registry = new Registry();
        $this->registry->setLoggerFactory($this->loggerFactory);
        CorePluginInitialization::initialize($this->registry);
    }
}
