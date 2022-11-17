<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\Model\Form\FieldInterface;

class IfValueMapper extends ValueMapper
{
    protected const WEIGHT = -1;

    /**
     * @param string|FieldInterface|null $fieldValue
     * @return string|FieldInterface|null
     */
    protected function resolveValue($fieldValue)
    {
        $result = $this->resolveEvaluation($this->configuration);
        if ($result !== null) {
            return $this->resolveValueMap($result, $fieldValue);
        }
        return null;
    }
}
