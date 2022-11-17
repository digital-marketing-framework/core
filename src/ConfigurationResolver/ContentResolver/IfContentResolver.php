<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

class IfContentResolver extends ContentResolver
{
    protected const WEIGHT = -1;

    public function finish(&$result): bool
    {
        $evalResult = $this->resolveEvaluation($this->configuration);
        if ($evalResult !== null) {
            $result = $this->resolveContent($evalResult);
            return true;
        }
        return false;
    }
}
