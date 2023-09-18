<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataProcessorPluginTest;

abstract class DataMapperTest extends DataProcessorPluginTest
{
    protected const DEFAULT_CONFIG = [];

    protected DataMapperInterface $subject;

    /**
     * @param array<string,mixed> $config
     * @param ?array<string,string|ValueInterface|null> $target
     * @param ?array<string,mixed> $defaultConfig
     */
    protected function processDataMapper(array $config, ?array $target = null, ?array $defaultConfig = null): DataInterface
    {
        if ($defaultConfig === null) {
            $defaultConfig = static::DEFAULT_CONFIG;
        }

        $class = static::CLASS_NAME;
        $this->subject = new $class(static::KEYWORD, $this->registry, $config, $this->getContext());
        $this->subject->setDataProcessor($this->dataProcessor);
        $this->subject->setDefaultConfiguration($defaultConfig);

        $target = new Data($target ?? []);
        $this->subject->mapData($target);

        return $target;
    }

    /**
     * @param array<string,string|ValueInterface|null> $inputData
     * @param array<string,string|ValueInterface|null> $expectedOutputData
     * @param ?array<string,mixed> $config
     * @param ?array<string,string|ValueInterface|null> $target
     * @param ?array<string,mixed> $defaultConfig
     */
    protected function mapData(array $inputData, array $expectedOutputData, ?array $config = null, ?array $target = null, ?array $defaultConfig = null): void
    {
        $this->data = $inputData;
        $config ??= [];
        $output = $this->processDataMapper($config, $target, $defaultConfig);
        $this->assertMultiValueEquals($expectedOutputData, $output);
    }
}
