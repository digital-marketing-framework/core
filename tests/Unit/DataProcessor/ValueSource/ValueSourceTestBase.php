<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataProcessorPluginTestBase;

/**
 * @template ValueSourceClass of ValueSource
 */
abstract class ValueSourceTestBase extends DataProcessorPluginTestBase
{
    protected const DEFAULT_CONFIG = [];

    /** @var ValueSourceClass */
    protected ValueSource $subject;

    protected function processObjectAwareness(): void
    {
        $this->subject->setDataProcessor($this->dataProcessor);
        $this->subject->setLogger($this->logger);
    }

    /**
     * @param array<string,mixed> $config
     * @param ?array<string,mixed> $defaultConfig
     */
    protected function processValueSource(array $config, ?array $defaultConfig = null): string|ValueInterface|null
    {
        if ($defaultConfig === null) {
            $defaultConfig = static::DEFAULT_CONFIG;
        }

        $class = static::CLASS_NAME;
        $this->subject = new $class(static::KEYWORD, $this->registry, $config, $this->getContext());
        $this->processObjectAwareness();
        $this->subject->setDefaultConfiguration($defaultConfig);

        return $this->subject->build();
    }
}
