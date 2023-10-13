<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;

interface GlobalConfigurationRegistryInterface extends PackageAliasesRegistryInterface
{
    public function getGlobalConfiguration(): GlobalConfigurationInterface;

    public function setGlobalConfiguration(GlobalConfigurationInterface $globalConfiguration): void;
}
