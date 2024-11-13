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

    protected function getIntegrationMapConfig(string $key, mixed $default = null): array
    {
        return MapUtility::flatten($this->getIntegrationConfig($key, $default));
    }

    protected function getIntegrationListConfig(string $key, mixed $default = null) : array
    {
        return ListUtility::flatten($this->getIntegrationConfig($key, $default));
    }
}
