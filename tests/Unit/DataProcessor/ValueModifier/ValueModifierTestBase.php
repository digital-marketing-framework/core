<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifier;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataProcessorPluginTestBase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

abstract class ValueModifierTestBase extends DataProcessorPluginTestBase
{
    protected const DEFAULT_CONFIG = [];

    protected ValueModifier $subject;

    /**
     * @param array<string,mixed> $config
     * @param ?array<string,mixed> $defaultConfig
     */
    protected function processValueModifier(array $config, string|ValueInterface|null $value, ?array $defaultConfig = null): string|ValueInterface|null
    {
        if ($defaultConfig === null) {
            $defaultConfig = static::DEFAULT_CONFIG;
        }

        $class = static::CLASS_NAME;
        $this->subject = new $class(static::KEYWORD, $this->registry, $config, $this->getContext());
        $this->subject->setDataProcessor($this->dataProcessor);
        $this->subject->setDefaultConfiguration($defaultConfig);

        return $this->subject->modify($value);
    }

    /**
     * @return array<array{0:mixed,1:mixed,2:?array<string,mixed>}>
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
