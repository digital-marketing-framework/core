<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class IfValueMapper extends ValueMapper
{
    protected const WEIGHT = -1;

    protected function resolveValue(string|ValueInterface|null $fieldValue): string|ValueInterface|null
    {
        // resolve negated path as well so that the fields inside have a chance to get tracked
        $negatedConfiguration = GeneralEvaluation::negateEvaluationConfiguration($this->configuration);
        $negatedEvalResult = $this->resolveEvaluation($negatedConfiguration);
        if ($negatedEvalResult !== null) {
            $this->resolveValueMap($negatedEvalResult, $fieldValue);
        }

        $result = $this->resolveEvaluation($this->configuration);
        if ($result !== null) {
            return $this->resolveValueMap($result, $fieldValue);
        }
        return null;
    }
}
