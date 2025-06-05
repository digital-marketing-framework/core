<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\Condition;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataProcessorPluginTestBase;

abstract class ConditionTestBase extends DataProcessorPluginTestBase
{
    protected const DEFAULT_CONFIG = [];

    protected Condition $subject;

    /**
     * @param array<string,mixed> $config
     * @param ?array<string,mixed> $defaultConfig
     */
    protected function processCondition(array $config, ?DataProcessorContextInterface $context = null, ?array $defaultConfig = null): bool
    {
        if ($defaultConfig === null) {
            $defaultConfig = static::DEFAULT_CONFIG;
        }

        $class = static::CLASS_NAME;
        $this->subject = new $class(static::KEYWORD, $this->registry, $config, $context ?? $this->getContext());
        $this->subject->setDataProcessor($this->dataProcessor);
        $this->subject->setDefaultConfiguration($defaultConfig);

        return $this->subject->evaluate();
    }
}
