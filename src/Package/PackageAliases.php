<?php

namespace DigitalMarketingFramework\Core\Package;

class PackageAliases implements PackageAliasesInterface
{
    /** @var array<string,string> */
    protected array $aliases = [];

    public function addAlias(string $packageName, string $alias): void
    {
        if ($alias !== '') {
            $this->aliases[$packageName] = $alias;
        }
    }

    public function resolveAlias(string $packageName): string
    {
        return $this->aliases[$packageName] ?? $packageName;
    }
}
