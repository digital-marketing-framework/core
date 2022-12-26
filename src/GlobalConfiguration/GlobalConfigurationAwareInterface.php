<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

interface GlobalConfigurationAwareInterface
{
    public function setGlobalConfiguration(ConfigurationInterface $globalConfiguration): void;
}
