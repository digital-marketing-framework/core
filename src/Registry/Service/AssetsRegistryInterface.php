<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

interface AssetsRegistryInterface
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
    public function getFrontendScripts(): array;
}
