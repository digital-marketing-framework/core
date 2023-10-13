<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Package\PackageAliasesInterface;

interface PackageAliasesRegistryInterface
{
    public function addPackageAlias(string $packageName, string $alias): void;

    public function getPackageAliases(): PackageAliasesInterface;
}
