<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

use DigitalMarketingFramework\Core\Package\PackageAliases;
use DigitalMarketingFramework\Core\Package\PackageAliasesInterface;

class DefaultGlobalConfiguration implements GlobalConfigurationInterface
{
    /** @var array<string,mixed> */
    protected array $config = [];

    protected PackageAliasesInterface $packageAliases;

    public function __construct()
    {
        $this->packageAliases = new PackageAliases();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $key = $this->packageAliases->resolveAlias($key);

        return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $key = $this->packageAliases->resolveAlias($key);
        $this->config[$key] = $value;
    }

    public function setPackageAliases(PackageAliasesInterface $packageAliases): void
    {
        $this->packageAliases = $packageAliases;
    }
}
