<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

trait GlobalConfigurationRegistryTrait
{
    protected ConfigurationInterface $globalConfiguration;

    public function getGlobalConfiguration(): ConfigurationInterface
    {
        if (!isset($this->globalConfiguration)) {
            $this->globalConfiguration = new Configuration([]);
        }
        return $this->globalConfiguration;
    }

    public function setGlobalConfiguration(ConfigurationInterface $globalConfiguration): void
    {
        $this->globalConfiguration = $globalConfiguration;
    }
}
