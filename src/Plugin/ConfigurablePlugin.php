<?php

namespace DigitalMarketingFramework\Core\Plugin;

use DigitalMarketingFramework\Core\Utility\ListUtility;
use DigitalMarketingFramework\Core\Utility\MapUtility;

abstract class ConfigurablePlugin extends Plugin implements ConfigurablePluginInterface
{
    protected array $configuration;
    protected array $defaultConfiguration;

    public function setDefaultConfiguration(array $defaultConfiguration): void
    {
        $this->defaultConfiguration = $defaultConfiguration;
    }

    public function getDefaultConfiguration(): array
    {
        return $this->defaultConfiguration;
    }

    protected function getMapConfig(string $key, mixed $default = [], ?array $configuration = null, ?array $defaultConfiguration = null): array
    {
        return MapUtility::flatten($this->getConfig($key, $default, $configuration, $defaultConfiguration));
    }

    protected function getListConfig(string $key, mixed $default = [], ?array $configuration = null, ?array $defaultConfiguration = null): array
    {
        return ListUtility::flatten($this->getConfig($key, $default, $configuration, $defaultConfiguration));
    }

    protected function getConfig(string $key, mixed $default = null, ?array $configuration = null, ?array $defaultConfiguration = null): mixed
    {
        if ($default === null) {
            $defaultConfiguration = $defaultConfiguration ?? $this->getDefaultConfiguration();
            if (array_key_exists($key, $defaultConfiguration)) {
                $default = $defaultConfiguration[$key];
            }
        }
        $configuration = $configuration ?? $this->configuration;
        if (is_array($configuration) && array_key_exists($key, $configuration)) {
            return $configuration[$key];
        }
        return $default;
    }
}
