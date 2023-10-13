<?php

namespace DigitalMarketingFramework\Core\Package;

interface PackageAliasesInterface
{
    public function addAlias(string $packageName, string $alias): void;

    public function resolveAlias(string $packageName): string;
}
