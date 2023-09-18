<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContext;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorInterface;
use DigitalMarketingFramework\Core\DataProcessor\FieldTracker;
use DigitalMarketingFramework\Core\DataProcessor\FieldTrackerInterface;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Tests\ListMapTestTrait;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class DataProcessorPluginTest extends TestCase
{
    use ListMapTestTrait;
    use MultiValueTestTrait;

    protected const KEYWORD = '';

    protected const CLASS_NAME = '';

    protected RegistryInterface&MockObject $registry;

    protected DataProcessorInterface&MockObject $dataProcessor;

    protected FieldTrackerInterface $fieldTracker;

    /** @var array<string,string|ValueInterface|null> */
    protected array $data = [];

    /** @var array<int,array<string,mixed>> */
    protected array $configuration = [[]];

    protected function setUp(): void
    {
        $this->registry = $this->createMock(RegistryInterface::class);
        $this->dataProcessor = $this->createMock(DataProcessorInterface::class);
        $this->fieldTracker = new FieldTracker();
    }

    protected function getContext(): DataProcessorContextInterface
    {
        return new DataProcessorContext(new Data($this->data), new Configuration($this->configuration), $this->fieldTracker);
    }
}
