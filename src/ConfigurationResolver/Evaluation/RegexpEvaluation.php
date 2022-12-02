<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

class RegexpEvaluation extends Evaluation
{
    protected function evalValue($fieldValue): bool
    {
        $regExp = $this->resolveContent($this->configuration);
        return preg_match('/' . $regExp . '/', $fieldValue);
    }
}
