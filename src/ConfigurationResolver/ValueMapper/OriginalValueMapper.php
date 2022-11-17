<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class OriginalValueMapper extends ValueMapper
{
    public function resolveValue(string|ValueInterface|null $fieldValue): string|ValueInterface|null
    {
        if ($this->configuration) {
            return $fieldValue;
        }
        return null;
    }
}
