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
}
