<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationBehaviour;

abstract class AbstractClauseEvaluation extends Evaluation
{
    protected const KEY_FIELD = 'field';
    protected const KEY_INDEX = 'index';
    protected const KEY_MODIFY = 'modify';

    protected function getConfigurationBehaviour(): ConfigurationBehaviour
    {
        return ConfigurationBehaviour::ConvertScalarToArrayWithSelfValue;
    }

    abstract protected function initialValue(): bool;

    abstract protected function calculate(bool $result, EvaluationInterface $evaluation): bool;

    public function eval(): bool
    {
        $subEvaluations = [];

        if (array_key_exists(static::KEY_MODIFY, $this->configuration)) {
            $this->addModifierToContext($this->configuration[static::KEY_MODIFY]);
            unset($this->configuration[static::KEY_MODIFY]);
        }

        if (array_key_exists(static::KEY_FIELD, $this->configuration) && !is_array($this->configuration[static::KEY_FIELD])) {
            $this->addKeyToContext($this->configuration[static::KEY_FIELD]);
            unset($this->configuration[static::KEY_FIELD]);
        }

        if (array_key_exists(static::KEY_INDEX, $this->configuration) && !is_array($this->configuration[static::KEY_INDEX])) {
            $this->addIndexToContext($this->configuration[static::KEY_INDEX]);
            unset($this->configuration[static::KEY_INDEX]);
        }

        foreach ($this->configuration as $key => $value) {
            $evaluation = $this->resolveKeyword($key, $value);

            if (!$evaluation) {
                $context = $this->context->copy();
                if (!is_numeric($key)) {
                    $this->addKeyToContext($key, $context);
                }
                $evaluation = $this->resolveKeyword('general', $value, $context);
            }

            $subEvaluations[] = $evaluation;
        }

        $this->sortSubResolvers($subEvaluations);

        $result = $this->initialValue();
        foreach ($subEvaluations as $evaluation) {
            $result = $this->calculate($result, $evaluation);
        }
        return $result;
    }
}
