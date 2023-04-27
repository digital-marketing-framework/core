<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContext;
use DigitalMarketingFramework\Core\DataProcessor\FieldTracker;
use DigitalMarketingFramework\Core\DataProcessor\FieldTrackerInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifier;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataProcessorPluginTest;

abstract class DataMapperTest extends DataProcessorPluginTest
{
    protected array $data = [];
    protected array $configuration = [[]];
    protected FieldTrackerInterface $fieldTracker;

    public function setUp(): void
    {
        parent::setUp();
        $this->fieldTracker = new FieldTracker();
    }

    protected function passthroughDataFirst(): bool
    {
        return false;
    }

    protected function processDataMapper(array $dataMapperConfig): DataInterface
    {
        $config = [];
        if ($this->passthroughDataFirst()) {
            $config['passthroughFields'] = [DataMapper::KEY_ENABLED => true];
        }
        $config[static::KEYWORD] = $dataMapperConfig;
        $dataProcessor = $this->registry->getDataProcessor();
        return $dataProcessor->processDataMapper($config, new Data($this->data), new Configuration($this->configuration));
    }

    abstract public function mapDataProvider(): array;

    /**
     * @test
     * @dataProvider mapDataProvider
     */
    public function mapData(array $input, array $expected, ?array $config = null): void
    {
        $this->data = $input;
        $config = $config ?? [];
        $config[ValueModifier::KEY_ENABLED] = true;
        $output = $this->processDataMapper($config);
        if (is_array($expected)) {
            if (empty($expected)) {
                $this->assertMultiValueEmpty($output);
            } else {
                $this->assertMultiValueEquals($expected, $output);
            }
        } else {
            $this->assertEquals($expected, $output);
        }
    }
}
