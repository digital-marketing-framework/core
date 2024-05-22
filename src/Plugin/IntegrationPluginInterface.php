<?php

namespace DigitalMarketingFramework\Core\Plugin;

use DigitalMarketingFramework\Core\Integration\IntegrationInfo;

interface IntegrationPluginInterface extends ConfigurablePluginInterface
{
    public static function getDefaultIntegrationInfo(): IntegrationInfo;

    public function getIntegrationInfo(): IntegrationInfo;

    /**
     * @param array<string,mixed> $defaultIntegrationConfiguration
     */
    public function setDefaultIntegrationConfiguration(array $defaultIntegrationConfiguration): void;

    /**
     * @return array<string,mixed>
     */
    public function getDefaultIntegrationConfiguration(): array;
}
