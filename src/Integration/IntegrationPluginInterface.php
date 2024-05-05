<?php

namespace DigitalMarketingFramework\Core\Integration;

use DigitalMarketingFramework\Core\Plugin\ConfigurablePluginInterface;

interface IntegrationPluginInterface extends ConfigurablePluginInterface
{
    public static function getDefaultIntegrationInfo(): IntegrationInfo;

    public function getIntegrationInfo(): IntegrationInfo;
}
