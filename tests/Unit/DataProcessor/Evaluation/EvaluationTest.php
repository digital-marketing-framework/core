<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataProcessorPluginTest;

abstract class EvaluationTest extends DataProcessorPluginTest
{
    protected const DEFAULT_CONFIG = [];

    protected EvaluationInterface $subject;

    /**
     * @param array<string,mixed> $config
     * @param ?array<string,mixed> $defaultConfig
     */
    protected function processEvaluation(array $config, ?DataProcessorContextInterface $context = null, ?array $defaultConfig = null): bool
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
