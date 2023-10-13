<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Package\PackageAliases;
use DigitalMarketingFramework\Core\Package\PackageAliasesInterface;

trait PackageAliasesRegistryTrait
{
    protected PackageAliasesInterface $packageAliases;

    public function addPackageAlias(string $packageName, string $alias): void
    {
        $this->getPackageAliases()->addAlias($packageName, $alias);
    }

    public function getPackageAliases(): PackageAliasesInterface
    {
        if (!isset($this->packageAliases)) {
            $this->packageAliases = new PackageAliases();
        }

        return $this->packageAliases;
    }
}
