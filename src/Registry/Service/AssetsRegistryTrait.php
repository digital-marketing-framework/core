<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

trait AssetsRegistryTrait
{
    /** @var array<string,array<string>> */
    protected array $configurationEditorScripts = [];

    /** @var array<string,array<string,array<string>>> */
    protected array $frontendScripts = [];

    public function addConfigurationEditorScripts(string $package, array $paths): void
    {
        $scripts = $this->configurationEditorScripts[$package] ?? [];
        array_push($scripts, ...$paths);
        $this->configurationEditorScripts[$package] = array_unique($scripts);
    }

    public function getConfigurationEditorScripts(): array
    {
        return $this->configurationEditorScripts;
    }

    public function addFrontendScripts(string $type, string $package, array $paths): void
    {
        $scripts = $this->frontendScripts[$type][$package] ?? [];
        array_push($scripts, ...$paths);
        $this->frontendScripts[$type][$package] = array_unique($scripts);
    }

    public function getFrontendScripts(): array
    {
        return $this->frontendScripts;
    }
}
