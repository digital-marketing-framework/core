<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

interface GlobalConfigurationAwareInterface
{
    public function setGlobalConfiguration(GlobalConfigurationInterface $globalConfiguration): void;
}
