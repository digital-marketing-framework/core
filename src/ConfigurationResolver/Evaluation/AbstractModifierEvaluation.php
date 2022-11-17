<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

abstract class AbstractModifierEvaluation extends Evaluation
{
    protected function getModifierName(): string
    {
        return $this->getKeyword();
    }

    protected function getModifierConfiguration(): mixed
    {
        return true;
    }

    protected function getModifierObject(): array
    {
        return [$this->getModifierName() => $this->getModifierConfiguration()];
    }

    public function eval(): bool
    {
        $this->addModifierToContext($this->getModifierObject());
        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword('general', $this->configuration);
        return $evaluation->eval();
    }
}
