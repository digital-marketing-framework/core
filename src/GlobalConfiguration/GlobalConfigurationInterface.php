<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

use DigitalMarketingFramework\Core\Package\PackageAliasesInterface;

interface GlobalConfigurationInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function setPackageAliases(PackageAliasesInterface $packageAliases): void;
}
