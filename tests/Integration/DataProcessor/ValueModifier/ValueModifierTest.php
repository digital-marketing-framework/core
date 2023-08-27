<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContext;
use DigitalMarketingFramework\Core\DataProcessor\FieldTracker;
use DigitalMarketingFramework\Core\DataProcessor\FieldTrackerInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifier;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataProcessorPluginTest;

abstract class ValueModifierTest extends DataProcessorPluginTest
{
    protected array $data = [];
    protected array $configuration = [[]];
    protected FieldTrackerInterface $fieldTracker;

    public function setUp(): void
    {
        parent::setUp();
        $this->fieldTracker = new FieldTracker();
    }

    protected function processValueModifier(array $config, string|null|ValueInterface $value): string|null|ValueInterface
    {
        $dataProcessor = $this->registry->getDataProcessor();
        $context = new DataProcessorContext(new Data($this->data), new Configuration($this->configuration), $this->fieldTracker);
        return $dataProcessor->processValueModifiers(
            [
                'id1' => $this->createListItem($this->getValueModifierConfiguration($config), 'id1', 10),
            ],
            $value,
            $context
        );
    }

    abstract public function modifyProvider(): array;

    protected function runModify(mixed $input, mixed $expected, ?array $config, bool $enabled): void
    {
        $config = $config ?? [];
        $config[ValueModifier::KEY_ENABLED] = $enabled;
        $output = $this->processValueModifier($config, $this->convertMultiValues($input));
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

    /**
     * @test
     * @dataProvider modifyProvider
     */
    public function modify(mixed $input, mixed $expected, ?array $config = null): void
    {
        $this->runModify($input, $expected, $config, true);
        $this->runModify($input, $input, $config, false);
    }
}
