<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

class OriginalValueMapper extends ValueMapper
{
    public function resolveValue($fieldValue)
    {
        if ($this->configuration) {
            return $fieldValue;
        }
        return null;
    }
}
