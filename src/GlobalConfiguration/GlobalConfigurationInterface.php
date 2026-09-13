<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\GlobalSettingsInterface;
use DigitalMarketingFramework\Core\Package\PackageAliasesInterface;

interface GlobalConfigurationInterface
{
    public function get(string $key, mixed $default = null, bool $resolvePlaceholders = true): mixed;

    public function set(string $key, mixed $value): void;

    public function setPackageAliases(PackageAliasesInterface $packageAliases): void;

    /**
     * Resolves a settings object, filling in whatever the configuration does not state from
     * the defaults declared in the global configuration schema.
     *
     * Computing those defaults runs the schema processors, which are plugins. So this cannot
     * be used before plugins have been registered — during initServices(), for example, it
     * fails with "No default value schema processor found for keyword ...". Code that runs
     * that early has to read the raw configuration instead, via get().
     *
     * @template GlobalSettingsClass of GlobalSettingsInterface
     *
     * @param class-string<GlobalSettingsClass> $class
     * @param array<mixed> $arguments
     *
     * @return GlobalSettingsClass
     */
    public function getGlobalSettings(string $class, ...$arguments): GlobalSettingsInterface;
}
