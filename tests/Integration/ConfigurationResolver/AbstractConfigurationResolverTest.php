<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use DigitalMarketingFramework\Core\ConfigurationResolver\FieldTracker;
use DigitalMarketingFramework\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Log\LoggerInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Tests\Integration\RegistryTestTrait;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;
use PHPUnit\Framework\TestCase;

abstract class AbstractConfigurationResolverTest extends TestCase
{
    use RegistryTestTrait;
    use MultiValueTestTrait;

    protected array $configurationResolverContext;

    protected FieldTracker $fieldTracker;

    protected array $data = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->initRegistry();
        $this->data = [];
        $this->configurationResolverContext = [];
        $this->fieldTracker = new FieldTracker();
    }

    abstract protected function getGeneralResolverClass(): string;

    protected function executeResolver(GeneralConfigurationResolverInterface $resolver): mixed
    {
        return $resolver->resolve();
    }

    /**
     * This is the execution of the actual resolver process
     *
     * - build a submission based on the field data from $this->data
     * - instantiate the general resolver
     * - let the general resolver process the given configuration array $config
     * - return the processed result so that it can be compared to the expected outcome
     */
    protected function runResolverProcess(mixed $config): mixed
    {
        $this->loggerFactory->method('getLogger')->willReturn($this->createMock(LoggerInterface::class));

        $data = new Data($this->data);
        $context = new ConfigurationResolverContext($data, $this->configurationResolverContext, $this->fieldTracker);

        $resolverClass = $this->getGeneralResolverClass();
        $resolver = new $resolverClass('general', $this->registry, $config, $context);

        return $this->executeResolver($resolver);
    }
}
