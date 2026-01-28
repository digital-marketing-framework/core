<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;

class CoreSettings extends GlobalSettings
{
    public function __construct()
    {
        parent::__construct('core');
    }

    public function debug(): bool
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_DEBUG);
    }

    public function getDefaultTimezone(): string
    {
        $timezone = $this->get(CoreGlobalConfigurationSchema::KEY_DEFAULT_TIMEZONE);

        if ($timezone === CoreGlobalConfigurationSchema::VALUE_TIMEZONE_SERVER || $timezone === '') {
            return date_default_timezone_get();
        }

        return $timezone;
    }
}
