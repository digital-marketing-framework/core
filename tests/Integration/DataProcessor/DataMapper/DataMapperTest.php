<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;
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
    /** @var array<string,string|ValueInterface|null> */
    protected array $data = [];

    /** @var array<int,array<string,mixed>> */
    protected array $configuration = [[]];

    protected FieldTrackerInterface $fieldTracker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fieldTracker = new FieldTracker();
    }

    protected function passthroughDataFirst(): bool
    {
        return false;
    }

    /**
     * @param array<string,mixed> $dataMapperConfig
     */
    protected function processDataMapper(array $dataMapperConfig): DataInterface
    {
        $config = [];
        if ($this->passthroughDataFirst()) {
            $config['passthroughFields'] = [PassthroughFieldsDataMapper::KEY_ENABLED => true];
        }

        $config[static::KEYWORD] = $dataMapperConfig;
        $dataProcessor = $this->registry->getDataProcessor();

        return $dataProcessor->processDataMapper($config, new Data($this->data), new Configuration($this->configuration));
    }

    /**
     * @return array<array{0:array<string,string|ValueInterface|null>,1:array<string,string|ValueInterface|null>,2?:?array<string,mixed>}>
     */
    abstract public function mapDataProvider(): array;

    /**
     * @param array<string,string|ValueInterface|null> $input
     * @param array<string,string|ValueInterface|null> $expected
     * @param ?array<string,mixed> $config
     *
     * @test
     *
     * @dataProvider mapDataProvider
     */
    public function mapData(array $input, array $expected, ?array $config = null): void
    {
        $this->data = $input;
        $config ??= [];
        $config[ValueModifier::KEY_ENABLED] = true;
        $output = $this->processDataMapper($config);
        if ($expected === []) {
            $this->assertMultiValueEmpty($output);
        } else {
            $this->assertMultiValueEquals($expected, $output);
        }
    }
}
