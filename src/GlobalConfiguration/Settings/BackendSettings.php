<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;

class BackendSettings extends GlobalSettings
{
    public function __construct()
    {
        parent::__construct('core', CoreGlobalConfigurationSchema::KEY_BACKEND);
    }

    /**
     * One of "auto", "light" or "dark". Passed to the backend UI as-is; "auto"
     * means the backend takes the color scheme of the system it is embedded in.
     */
    public function getColorScheme(): string
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_BACKEND_COLOR_SCHEME);
    }
}
