<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Plugin\PluginInterface;
use DigitalMarketingFramework\Core\Registry\RegistryException;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

trait PluginRegistryTrait
{
    protected array $pluginClasses = [];
    protected array $pluginAdditionalArguments = [];

    abstract protected function createObject(string $class, array $arguments = []): object;
    abstract protected function classValidation(string $class, string $interface): void;
    abstract protected function interfaceValidation(string $interface, string $parentInterface): void;

    public function getPlugin(string $keyword, string $interface, array $arguments = []): ?PluginInterface
    {
        $class = $this->pluginClasses[$interface][$keyword] ?? null;
        $additionalArguments = $this->pluginAdditionalArguments[$interface][$keyword] ?? [];

        if (!$class) {
            if ($this->checkKeywordAsClass($keyword, $interface)) {
                $class = $keyword;
                $keyword = GeneralUtility::getPluginKeyword($keyword, $interface) ?: $keyword;
                $additionalArguments = [];
            }
        }

        if ($class && class_exists($class)) {
            $constructorArguments = [$keyword, $this];
            array_push($constructorArguments, ...$arguments);
            array_push($constructorArguments, ...$additionalArguments);
            return $this->createObject($class, $constructorArguments);
        }

        return null;
    }

    public function getAllPlugins(string $interface, array $arguments = []): array
    {
        $result = [];
        foreach (array_keys($this->pluginClasses[$interface] ?? []) as $keyword) {
            $result[$keyword] = $this->getPlugin($keyword, $interface, $arguments);
        }
        $this->sortPlugins($result);
        return $result;
    }

    public function registerPlugin(string $interface, string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        if (!$keyword || is_numeric($keyword)) {
            $keyword = GeneralUtility::getPluginKeyword($class, $interface) ?: $keyword;
        }
        $this->interfaceValidation($interface, PluginInterface::class);
        $this->classValidation($class, $interface);
        $this->pluginClasses[$interface][$keyword] = $class;
        $this->pluginAdditionalArguments[$interface][$keyword] = $additionalArguments;
    }

    public function deletePlugin(string $keyword, string $interface): void
    {
        if (isset($this->pluginClasses[$interface][$keyword])) {
            unset($this->pluginClasses[$interface][$keyword]);
        }
        if (isset($this->pluginAdditionalArguments[$interface][$keyword])) {
            unset($this->pluginAdditionalArguments[$interface][$keyword]);
        }
    }

    protected function checkKeywordAsClass(string $keyword, string $interface): bool
    {
        $result = false;
        if (class_exists($keyword)) {
            try {
                $this->classValidation($keyword, $interface);
                $result = true;
            } catch (RegistryException $e) {
                // keyword is not a class (or it is not implementing the desired interface)
            }
        }
        return $result;
    }

    public function sortPlugins(array &$plugins): void
    {
        uasort($plugins, function (PluginInterface $a, PluginInterface $b) {
            return $a->getWeight() <=> $b->getWeight();
        });
    }
}
