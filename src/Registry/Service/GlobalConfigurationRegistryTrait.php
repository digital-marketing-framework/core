<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\GlobalConfiguration\DefaultGlobalConfiguration;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;

trait GlobalConfigurationRegistryTrait
{
    protected GlobalConfigurationInterface $globalConfiguration;

    public function getGlobalConfiguration(): GlobalConfigurationInterface
    {
        if (!isset($this->globalConfiguration)) {
            $this->globalConfiguration = new DefaultGlobalConfiguration();
        }

        return $this->globalConfiguration;
    }

    public function setGlobalConfiguration(GlobalConfigurationInterface $globalConfiguration): void
    {
        $this->globalConfiguration = $globalConfiguration;
    }
}
