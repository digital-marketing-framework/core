<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\DataProcessorPluginTest;

abstract class EvaluationTest extends DataProcessorPluginTest
{
    protected EvaluationInterface $subject;

    protected function processEvaluation(array $config, ?DataProcessorContextInterface $context = null): bool
    {
        $class = static::CLASS_NAME;
        $this->subject = new $class(static::KEYWORD, $this->registry, $config, $context ?? $this->getContext());
        $this->subject->setDataProcessor($this->dataProcessor);
        return $this->subject->evaluate();
    }
}
