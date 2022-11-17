<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

interface GlobalConfigurationRegistryInterface
{
    public function getGlobalConfiguration(): ConfigurationInterface;
    public function setGlobalConfiguration(ConfigurationInterface $globalConfiguration): void;
}
