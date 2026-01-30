<?php

namespace DigitalMarketingFramework\Core\Plugin;

use DigitalMarketingFramework\Core\Integration\IntegrationInfo;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Utility\ListUtility;
use DigitalMarketingFramework\Core\Utility\MapUtility;

abstract class IntegrationPlugin extends ConfigurablePlugin implements IntegrationPluginInterface
{
    public function __construct(
        string $keyword,
        protected IntegrationInfo $integrationInfo,
        ConfigurationInterface $configuration,
    ) {
        parent::__construct($keyword);
        $this->integrationConfiguration = $configuration->getIntegrationConfiguration($integrationInfo->getName());
    }

    /** @var array<string,mixed> */
    protected array $integrationConfiguration = [];

    /** @var array<string,mixed> */
    protected array $defaultIntegrationConfiguration = [];

    public function setDefaultIntegrationConfiguration(array $defaultIntegrationConfiguration): void
    {
        $this->defaultIntegrationConfiguration = $defaultIntegrationConfiguration;
    }

    public function getDefaultIntegrationConfiguration(): array
    {
        return $this->defaultIntegrationConfiguration;
    }

    protected function getIntegrationConfig(string $key, mixed $default = null): mixed
    {
        return $this->getConfig($key, $default, $this->integrationConfiguration, $this->defaultIntegrationConfiguration);
    }

    protected function getIntegrationBoolConfig(string $key, ?bool $default = null): bool
    {
        return (bool)$this->getIntegrationConfig($key, $default);
    }

    protected function getIntegrationStringConfig(string $key, ?string $default = null): string
    {
        return (string)$this->getIntegrationConfig($key, $default);
    }

    protected function getIntegrationIntConfig(string $key, ?int $default = null): int
    {
        return (int)$this->getIntegrationConfig($key, $default);
    }

    /**
     * @param ?array<mixed> $default
     *
     * @return array<mixed>
     */
    protected function getIntegrationArrayConfig(string $key, ?array $default = null): array
    {
        return (array)$this->getIntegrationConfig($key, $default);
    }

    /**
     * @param ?array<mixed> $default
     *
     * @return array<string,mixed>
     */
    protected function getIntegrationMapConfig(string $key, ?array $default = null): array
    {
        return MapUtility::flatten($this->getIntegrationArrayConfig($key, $default));
    }

    /**
     * @param ?array<mixed> $default
     *
     * @return array<mixed>
     */
    protected function getIntegrationListConfig(string $key, ?array $default = null): array
    {
        return ListUtility::flatten($this->getIntegrationArrayConfig($key, $default));
    }
}
