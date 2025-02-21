<?php

namespace DigitalMarketingFramework\Core\Alert;

use DigitalMarketingFramework\Core\Model\Alert\AlertInterface;

interface AlertManagerInterface
{
    /**
     * @return array<AlertInterface>
     */
    public function getAllAlerts(): array;
}
