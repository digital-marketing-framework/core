<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContext;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorInterface;
use DigitalMarketingFramework\Core\DataProcessor\FieldTracker;
use DigitalMarketingFramework\Core\DataProcessor\FieldTrackerInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\CoreSettings;
use DigitalMarketingFramework\Core\Log\LoggerInterface;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Tests\ListMapTestTrait;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;
use DigitalMarketingFramework\Core\Tests\TestUtilityTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class DataProcessorPluginTestBase extends TestCase
{
    use ListMapTestTrait;
    use MultiValueTestTrait;
    use TestUtilityTrait;

    protected const KEYWORD = '';

    protected const CLASS_NAME = '';

    protected RegistryInterface&MockObject $registry;

    protected DataProcessorInterface&MockObject $dataProcessor;

    protected LoggerInterface&MockObject $logger;

    protected GlobalConfigurationInterface&MockObject $globalConfiguration;

    protected CoreSettings&MockObject $coreSettings;

    protected FieldTrackerInterface $fieldTracker;

    /** @var array<string,string|ValueInterface|null> */
    protected array $data = [];

    /** @var array<int,array<string,mixed>> */
    protected array $configuration = [[]];

    protected function setUp(): void
    {
        $this->registry = $this->createMock(RegistryInterface::class);
        $this->dataProcessor = $this->createMock(DataProcessorInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->fieldTracker = new FieldTracker();

        $this->coreSettings = $this->createMock(CoreSettings::class);
        $this->coreSettings->method('getDefaultTimezone')->willReturn(DateTimeValue::DEFAULT_TIMEZONE);

        $this->globalConfiguration = $this->createMock(GlobalConfigurationInterface::class);
        $this->globalConfiguration->method('getGlobalSettings')->with(CoreSettings::class)->willReturn($this->coreSettings);
    }

    protected function injectGlobalConfiguration(object $subject): void
    {
        if ($subject instanceof GlobalConfigurationAwareInterface) {
            $subject->setGlobalConfiguration($this->globalConfiguration);
        }
    }

    /**
     * Create a fresh logger mock for tests that need specific expectations.
     */
    protected function createLoggerMock(): LoggerInterface&MockObject
    {
        return $this->createMock(LoggerInterface::class);
    }

    protected function getContext(): DataProcessorContextInterface
    {
        return new DataProcessorContext(new Data($this->data), new Configuration($this->configuration), $this->fieldTracker);
    }
}
