<?php

namespace DigitalMarketingFramework\Core\Plugin;

use DigitalMarketingFramework\Core\Utility\ListUtility;
use DigitalMarketingFramework\Core\Utility\MapUtility;

abstract class ConfigurablePlugin extends Plugin implements ConfigurablePluginInterface
{
    /** @var array<string,mixed> */
    protected array $configuration = [];

    /** @var array<string,mixed> */
    protected array $defaultConfiguration = [];

    public function setDefaultConfiguration(array $defaultConfiguration): void
    {
        $this->defaultConfiguration = $defaultConfiguration;
    }

    public function getDefaultConfiguration(): array
    {
        return $this->defaultConfiguration;
    }

    /**
     * @param ?array<string,mixed> $configuration
     * @param ?array<string,mixed> $defaultConfiguration
     */
    protected function getBoolConfig(string $key, ?bool $default = null, ?array $configuration = null, ?array $defaultConfiguration = null): bool
    {
        return (bool)$this->getConfig($key, $default, $configuration, $defaultConfiguration);
    }

    /**
     * @param ?array<string,mixed> $configuration
     * @param ?array<string,mixed> $defaultConfiguration
     */
    protected function getStringConfig(string $key, ?string $default = null, ?array $configuration = null, ?array $defaultConfiguration = null): string
    {
        return (string)$this->getConfig($key, $default, $configuration, $defaultConfiguration);
    }

    /**
     * @param ?array<string,mixed> $configuration
     * @param ?array<string,mixed> $defaultConfiguration
     */
    protected function getIntConfig(string $key, ?int $default = null, ?array $configuration = null, ?array $defaultConfiguration = null): int
    {
        return (int)$this->getConfig($key, $default, $configuration, $defaultConfiguration);
    }

    /**
     * @param ?array<mixed> $default
     * @param ?array<string,mixed> $configuration
     * @param ?array<string,mixed> $defaultConfiguration
     *
     * @return array<mixed>
     */
    protected function getArrayConfig(string $key, ?array $default = null, ?array $configuration = null, ?array $defaultConfiguration = null): array
    {
        return (array)$this->getConfig($key, $default, $configuration, $defaultConfiguration);
    }

    /**
     * @param ?array<mixed> $default
     * @param ?array<string,mixed> $configuration
     * @param ?array<string,mixed> $defaultConfiguration
     *
     * @return array<string,mixed>
     */
    protected function getMapConfig(string $key, ?array $default = null, ?array $configuration = null, ?array $defaultConfiguration = null): array
    {
        return MapUtility::flatten($this->getArrayConfig($key, $default, $configuration, $defaultConfiguration));
    }

    /**
     * @param ?array<mixed> $default
     * @param ?array<string,mixed> $configuration
     * @param ?array<string,mixed> $defaultConfiguration
     *
     * @return array<mixed>
     */
    protected function getListConfig(string $key, ?array $default = null, ?array $configuration = null, ?array $defaultConfiguration = null): array
    {
        return ListUtility::flatten($this->getArrayConfig($key, $default, $configuration, $defaultConfiguration));
    }

    /**
     * @param ?array<string,mixed> $configuration
     * @param ?array<string,mixed> $defaultConfiguration
     */
    protected function getConfig(string $key, mixed $default = null, ?array $configuration = null, ?array $defaultConfiguration = null): mixed
    {
        if ($default === null) {
            $defaultConfiguration ??= $this->getDefaultConfiguration();
            if (array_key_exists($key, $defaultConfiguration)) {
                $default = $defaultConfiguration[$key];
            }
        }

        $configuration ??= $this->configuration;
        if (array_key_exists($key, $configuration)) {
            return $configuration[$key];
        }

        return $default;
    }

    public static function getLabel(): ?string
    {
        return null;
    }
}
