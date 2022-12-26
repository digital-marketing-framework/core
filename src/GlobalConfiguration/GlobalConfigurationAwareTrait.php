<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

trait GlobalConfigurationAwareTrait
{
    protected ConfigurationInterface $globalConfiguration;

    public function setGlobalConfiguration(ConfigurationInterface $globalConfiguration): void
    {
        $this->globalConfiguration = $globalConfiguration;
    }
}
