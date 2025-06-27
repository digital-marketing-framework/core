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
     * @template GlobalSettingsClass of GlobalSettingsInterface
     *
     * @param class-string<GlobalSettingsClass> $class
     * @param array<mixed> $arguments
     *
     * @return GlobalSettingsClass
     */
    public function getGlobalSettings(string $class, ...$arguments): GlobalSettingsInterface;
}
