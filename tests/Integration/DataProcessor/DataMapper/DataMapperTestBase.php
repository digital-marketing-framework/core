<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContext;
use DigitalMarketingFramework\Core\DataProcessor\FieldTracker;
use DigitalMarketingFramework\Core\DataProcessor\FieldTrackerInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifier;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataProcessorPluginTestBase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

abstract class DataMapperTestBase extends DataProcessorPluginTestBase
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

        $context = new DataProcessorContext(new Data($this->data), new Configuration($this->configuration), $this->fieldTracker);

        return $dataProcessor->processDataMapper($config, $context);
    }

    /**
     * @return array<array{0:array<string,string|ValueInterface|null>,1:array<string,string|ValueInterface|null>,2?:?array<string,mixed>}>
     */
    abstract public static function mapDataProvider(): array;

    /**
     * @param array<string,string|ValueInterface|null> $input
     * @param array<string,string|ValueInterface|null> $expected
     * @param ?array<string,mixed> $config
     */
    #[Test]
    #[DataProvider('mapDataProvider')]
    public function mapData(array $input, array $expected, ?array $config = null): void
    {
        $this->data = $input;
        $config ??= [];
        $config[ValueModifier::KEY_ENABLED] = true;
        $output = $this->processDataMapper($config);
        if ($expected === []) {
            static::assertMultiValueEmpty($output);
        } else {
            static::assertMultiValueEquals($expected, $output);
        }
    }
}
