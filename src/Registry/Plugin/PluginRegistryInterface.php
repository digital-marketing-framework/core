<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Plugin\PluginInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

interface PluginRegistryInterface extends RegistryInterface
{
    public function getPlugin(string $keyword, string $interface, array $arguments = []): ?PluginInterface;
    
    /**
     * @param array<mixed> $arguments
     * @return array<PluginInterface>
     */
    public function getAllPlugins(string $interface, array $arguments = []): array;
    public function registerPlugin(string $interface, string $class, array $additionalArguments = [], string $keyword = ''): void;
    public function deletePlugin(string $keyword, string $interface): void;
    public function sortPlugins(array &$plugins): void;
}
