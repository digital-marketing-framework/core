<?php

namespace DigitalMarketingFramework\Core\Plugin;

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

    protected function getConfig(string $key, $default = null, ?array $configuration = null, ?array $defaultConfiguration = null): mixed
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
