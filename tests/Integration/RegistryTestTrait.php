<?php

namespace DigitalMarketingFramework\Core\Tests\Integration;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\CoreInitialization;
use DigitalMarketingFramework\Core\Log\LoggerFactoryInterface;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;
use PHPUnit\Framework\MockObject\MockObject;

trait RegistryTestTrait
{
    protected ContextInterface&MockObject $context;

    protected LoggerFactoryInterface&MockObject $loggerFactory;

    protected ConfigurationDocumentManagerInterface&MockObject $configurationDocumentManager;

    protected ConfigurationDocumentStorageInterface&MockObject $configurationDocumentStorage;

    protected ConfigurationDocumentParserInterface&MockObject $configurationDocumentParser;

    abstract protected function createRegistry(): void;

    protected function initRegistry(): void
    {
        // mock everything from the outside world
        $this->context = $this->createMock(ContextInterface::class);
        $this->loggerFactory = $this->createMock(LoggerFactoryInterface::class);
        $this->configurationDocumentStorage = $this->createMock(ConfigurationDocumentStorageInterface::class);
        $this->configurationDocumentParser = $this->createMock(ConfigurationDocumentParserInterface::class);
        $this->configurationDocumentManager = $this->createMock(ConfigurationDocumentManagerInterface::class);
        $this->configurationDocumentManager->method('getStorage')->willReturn($this->configurationDocumentStorage);
        $this->configurationDocumentManager->method('getParser')->willReturn($this->configurationDocumentParser);
        $this->configurationDocumentManager->method('getConfigurationStackFromConfiguration')->willReturnCallback(static function ($configuration) {
            return [$configuration];
        });

        // initialize the rest regularly
        $this->createRegistry();
        $this->registry->setContext($this->context);
        $this->registry->setLoggerFactory($this->loggerFactory);
        $this->registry->setConfigurationDocumentManager($this->configurationDocumentManager);

        // init plugins
        $coreInitialization = new CoreInitialization();
        $coreInitialization->initMetaData($this->registry);
        $coreInitialization->initGlobalConfiguration(RegistryDomain::CORE, $this->registry);
        $coreInitialization->initServices(RegistryDomain::CORE, $this->registry);
        $coreInitialization->initPlugins(RegistryDomain::CORE, $this->registry);
    }
}
