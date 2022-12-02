<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class SelfValueMapper extends ValueMapper
{
    protected const WEIGHT = 20;

    public function resolveValue(string|ValueInterface|null $fieldValue): string|ValueInterface|null
    {
        return $this->configuration;
    }
}
