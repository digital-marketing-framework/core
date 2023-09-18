<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

trait GlobalConfigurationAwareTrait
{
    protected GlobalConfigurationInterface $globalConfiguration;

    public function setGlobalConfiguration(GlobalConfigurationInterface $globalConfiguration): void
    {
        $this->globalConfiguration = $globalConfiguration;
    }
}
