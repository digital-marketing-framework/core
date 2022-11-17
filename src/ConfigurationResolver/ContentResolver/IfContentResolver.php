<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class IfContentResolver extends ContentResolver
{
    protected const WEIGHT = -1;

    public function finish(string|ValueInterface|null &$result): bool
    {
        // resolve negated path as well so that the fields inside have a chance to get tracked
        $negatedConfiguration = GeneralEvaluation::negateEvaluationConfiguration($this->configuration);
        $negatedEvalResult = $this->resolveEvaluation($negatedConfiguration);
        if ($negatedEvalResult !== null) {
            $this->resolveContent($negatedEvalResult);
        }

        $evalResult = $this->resolveEvaluation($this->configuration);
        if ($evalResult !== null) {
            $result = $this->resolveContent($evalResult);
            return true;
        }
        return false;
    }
}
