<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

class SelfValueMapper extends ValueMapper
{
    protected const WEIGHT = 20;

    public function resolveValue($fieldValue): string
    {
        return $this->configuration;
    }
}
