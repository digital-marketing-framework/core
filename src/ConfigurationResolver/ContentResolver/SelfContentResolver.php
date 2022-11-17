<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

class SelfContentResolver extends ContentResolver
{
    protected const WEIGHT = 0;

    public function build()
    {
        return $this->configuration;
    }
}
