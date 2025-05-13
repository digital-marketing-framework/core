<?php

namespace DigitalMarketingFramework\Core\Alert;

use DigitalMarketingFramework\Core\Model\Alert\AlertInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface AlertHandlerInterface extends PluginInterface
{
    /**
     * @return array<AlertInterface>
     */
    public function getAlerts(): array;
}
