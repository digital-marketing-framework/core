<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

trait AssetsRegistryTrait
{
    /** @var array<string,array<string>> */
    protected array $configurationEditorScripts = [];

    /** @var array<string,array<string,array<string>>> */
    protected array $frontendScripts = [];

    /** @var array<string,array<string,array<string,bool>>> */
    protected array $activeFrontendScripts = [];

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

    public function activateFrontendScript(string $type, string $package, string $path): void
    {
        $this->activeFrontendScripts[$type][$package][$path] = true;
    }

    public function resetActiveFrontendScripts(): void
    {
        $this->activeFrontendScripts = [];
    }

    public function getFrontendScripts(bool $onlyActive = false): array
    {
        if (!$onlyActive) {
            return $this->frontendScripts;
        }

        $result = [];
        foreach ($this->frontendScripts as $type => $packages) {
            foreach ($packages as $package => $scripts) {
                foreach ($scripts as $script) {
                    if ($this->activeFrontendScripts[$type][$package][$script] ?? false) {
                        $result[$type][$package][] = $script;
                    }
                }
            }
        }

        return $result;
    }
}
