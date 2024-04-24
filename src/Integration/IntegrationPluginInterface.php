<?php

namespace DigitalMarketingFramework\Core\Integration;

use DigitalMarketingFramework\Core\Plugin\ConfigurablePluginInterface;

interface IntegrationPluginInterface extends ConfigurablePluginInterface
{
    public static function getIntegrationName(): string;
    public static function getIntegrationLabel(): ?string;

    public static function getIntegrationWeight(): int;
}
