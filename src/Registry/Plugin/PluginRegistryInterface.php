<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface PluginRegistryInterface
{
    /**
     * @param array<mixed> $arguments
     */
    public function getPlugin(string $keyword, string $interface, array $arguments = []): ?PluginInterface;

    /**
     * @param array<mixed> $arguments
     *
     * @return array<PluginInterface>
     */
    public function getAllPlugins(string $interface, array $arguments = []): array;

    /**
     * @return array<string,string>
     */
    public function getAllPluginClasses(string $interface): array;

    public function getPluginClass(string $interface, string $keyword): ?string;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerPlugin(string $interface, string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deletePlugin(string $keyword, string $interface): void;

    /**
     * @param array<PluginInterface> $plugins
     */
    public function sortPlugins(array &$plugins): void;
}
