<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface PluginRegistryInterface
{
    /**
     * @template PluginTypeInterface of PluginInterface
     *
     * @param class-string<PluginTypeInterface> $interface
     * @param array<mixed> $arguments
     *
     * @return ?PluginTypeInterface
     */
    public function getPlugin(string $keyword, string $interface, array $arguments = []): ?PluginInterface;

    /**
     * @template PluginTypeInterface of PluginInterface
     *
     * @param class-string<PluginTypeInterface> $interface
     * @param array<mixed> $arguments
     *
     * @return array<PluginTypeInterface>
     */
    public function getAllPlugins(string $interface, array $arguments = []): array;

    /**
     * @template PluginTypeInterface of PluginInterface
     *
     * @param class-string<PluginTypeInterface> $interface
     *
     * @return array<string,class-string<PluginTypeInterface>>
     */
    public function getAllPluginClasses(string $interface): array;

    /**
     * @template PluginTypeInterface of PluginInterface
     *
     * @param class-string<PluginTypeInterface> $interface
     *
     * @return class-string<PluginTypeInterface>
     */
    public function getPluginClass(string $interface, string $keyword): ?string;

    /**
     * @template PluginTypeInterface of PluginInterface
     *
     * @param class-string<PluginTypeInterface> $interface
     * @param class-string<PluginTypeInterface> $class
     * @param array<mixed> $additionalArguments
     */
    public function registerPlugin(string $interface, string $class, array $additionalArguments = [], string $keyword = ''): void;

    /**
     * @param class-string<PluginInterface> $interface
     */
    public function deletePlugin(string $keyword, string $interface): void;

    /**
     * @param array<PluginInterface> $plugins
     */
    public function sortPlugins(array &$plugins): void;
}
