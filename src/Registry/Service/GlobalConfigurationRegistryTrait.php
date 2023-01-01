<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

trait GlobalConfigurationRegistryTrait
{
    protected ConfigurationInterface $globalConfiguration;

    public function getGlobalConfiguration(): ConfigurationInterface
    {
        return $this->globalConfiguration;
    }

    public function setGlobalConfiguration(ConfigurationInterface $globalConfiguration): void
    {
        $this->globalConfiguration = $globalConfiguration;
    }
}
