<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class IfValueMapper extends ValueMapper
{
    protected const WEIGHT = -1;

    protected function resolveValue(string|ValueInterface|null $fieldValue): string|ValueInterface|null
    {
        $result = $this->resolveEvaluation($this->configuration);
        if ($result !== null) {
            return $this->resolveValueMap($result, $fieldValue);
        }
        return null;
    }
}
