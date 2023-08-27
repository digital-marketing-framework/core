<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataProcessorPluginTest;

abstract class DataMapperTest extends DataProcessorPluginTest
{
    protected const DEFAULT_CONFIG = [];

    protected DataMapperInterface $subject;

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

    protected function mapData(array $inputData, array $expectedOutputData, ?array $config = null, ?array $target = null, ?array $defaultConfig = null): void
    {
        $this->data = $inputData;
        $config = $config ?? [];
        $output = $this->processDataMapper($config, $target, $defaultConfig);
        $this->assertMultiValueEquals($expectedOutputData, $output);
    }
}
