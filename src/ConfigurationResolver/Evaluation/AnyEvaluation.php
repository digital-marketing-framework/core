<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

class AnyEvaluation extends Evaluation
{
    protected function evalValue($fieldValue)
    {
        return $this->evaluate($this->configuration);
    }

    protected function initialValue(): bool
    {
        return false;
    }

    protected function calculateResult(bool $indexResult, bool $overallResult)
    {
        return $indexResult || $overallResult;
    }

    protected function evalMultiValue(MultiValue $fieldValue): bool
    {
        $result = $this->initialValue();
        foreach ($fieldValue as $index => $value) {
            $context = $this->context->copy();
            $this->addIndexToContext($index, $context);
            $result = $this->calculateResult($this->evaluate($this->configuration, $context), $result);
        }
        return $result;
    }
}
