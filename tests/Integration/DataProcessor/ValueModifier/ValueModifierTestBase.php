<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContext;
use DigitalMarketingFramework\Core\DataProcessor\FieldTracker;
use DigitalMarketingFramework\Core\DataProcessor\FieldTrackerInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifier;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\DataProcessorPluginTestBase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

abstract class ValueModifierTestBase extends DataProcessorPluginTestBase
{
    /** @var array<string,string|ValueInterface|null> */
    protected array $data = [];

    /** @var array<array<string,mixed>> */
    protected array $configuration = [[]];

    protected FieldTrackerInterface $fieldTracker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fieldTracker = new FieldTracker();
    }

    /**
     * @param array<string,mixed> $config
     */
    protected function processValueModifier(array $config, string|ValueInterface|null $value): string|ValueInterface|null
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

    /**
     * @return array<array{0:mixed,1:mixed,2?:?array<string,mixed>}>
     */
    abstract public static function modifyProvider(): array;

    /**
     * @param ?array<string,mixed> $config
     */
    protected function runModify(mixed $input, mixed $expected, ?array $config, bool $enabled): void
    {
        $config ??= [];
        $config[ValueModifier::KEY_ENABLED] = $enabled;
        $output = $this->processValueModifier($config, $this->convertMultiValues($input));
        if (is_array($expected)) {
            if ($expected === []) {
                $this->assertMultiValueEmpty($output);
            } else {
                $this->assertMultiValueEquals($expected, $output);
            }
        } else {
            $this->assertEquals($expected, $output);
        }
    }

    /**
     * @param ?array<string,mixed> $config
     */
    #[Test]
    #[DataProvider('modifyProvider')]
    public function modify(mixed $input, mixed $expected, ?array $config = null): void
    {
        $this->runModify($input, $expected, $config, true);
        $this->runModify($input, $input, $config, false);
    }
}
