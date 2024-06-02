<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

interface ScriptAssetsRegistryInterface
{
    /**
     * @param array<string> $paths
     */
    public function addConfigurationEditorScripts(string $package, array $paths): void;

    /**
     * @return array<string,array<string>>
     */
    public function getConfigurationEditorScripts(): array;

    /**
     * @param array<string> $paths
     */
    public function addFrontendScripts(string $type, string $package, array $paths): void;

    /**
     * @return array<string,array<string,array<string>>>
     */
    public function getFrontendScripts(bool $onlyActive = false): array;

    public function activateFrontendScript(string $type, string $package, string $path): void;

    public function resetActiveFrontendScripts(): void;
}
