<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

class KeyEvaluation extends Evaluation
{
    public function eval(): bool
    {
        $this->context['useKey'] = true;
        /** @var GeneralEvaluation $evaluation */
        $evaluation = $this->resolveKeyword('general', $this->configuration);
        return $evaluation->eval();
    }
}
