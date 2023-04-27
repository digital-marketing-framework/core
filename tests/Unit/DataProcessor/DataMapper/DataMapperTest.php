<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataProcessorPluginTest;

abstract class DataMapperTest extends DataProcessorPluginTest
{
    protected DataMapperInterface $subject;

    protected function processDataMapper(array $config, ?array $target = null): DataInterface
    {
        $class = static::CLASS_NAME;
        $this->subject = new $class(static::KEYWORD, $this->registry, $config, $this->getContext());
        $this->subject->setDataProcessor($this->dataProcessor);
        $target = new Data($target ?? []);
        $this->subject->mapData($target);
        return $target;
    }

    protected function mapData(array $inputData, array $expectedOutputData, ?array $config = null, ?array $target = null): void
    {
        $this->data = $inputData;
        $config = $config ?? [];
        $config[DataMapper::KEY_ENABLED] = true;
        $output = $this->processDataMapper($config, $target);
        $this->assertMultiValueEquals($expectedOutputData, $output);
    }
}
