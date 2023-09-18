<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataProcessorPluginTest;

abstract class ValueSourceTest extends DataProcessorPluginTest
{
    protected const DEFAULT_CONFIG = [];

    protected ValueSourceInterface $subject;

    /**
     * @param array<string,mixed> $config
     * @param ?array<string,mixed> $defaultConfig
     */
    protected function processValueSource(array $config, ?array $defaultConfig = null): string|null|ValueInterface
    {
        if ($defaultConfig === null) {
            $defaultConfig = static::DEFAULT_CONFIG;
        }

        $class = static::CLASS_NAME;
        $this->subject = new $class(static::KEYWORD, $this->registry, $config, $this->getContext());
        $this->subject->setDataProcessor($this->dataProcessor);
        $this->subject->setDefaultConfiguration($defaultConfig);

        return $this->subject->build();
    }
}
