<?php

namespace DigitalMarketingFramework\Core\Tests\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\GlobalSettings;

class GenericGlobalSettings extends GlobalSettings
{
    /**
     * @return array<string,mixed>
     */
    public function getInjectedSettings(): array
    {
        return $this->settings;
    }
}
