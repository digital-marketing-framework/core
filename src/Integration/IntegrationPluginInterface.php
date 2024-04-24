<?php

namespace DigitalMarketingFramework\Core\Integration;

use DigitalMarketingFramework\Core\Plugin\ConfigurablePluginInterface;

interface IntegrationPluginInterface extends ConfigurablePluginInterface
{
    public const INTEGRATION_WEIGHT_DEFAULT = 50;

    public const INTEGRATION_WEIGHT_TOP = 10;

    public const INTEGRATION_WEIGHT_BOTTOM = 100;

    public static function getIntegrationName(): string;

    public static function getIntegrationLabel(): ?string;

    public static function getIntegrationIcon(): ?string;

    public static function getIntegrationWeight(): int;
}
