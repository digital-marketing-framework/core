<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;

class FrontendSettings extends GlobalSettings
{
    public function __construct()
    {
        parent::__construct('core', CoreGlobalConfigurationSchema::KEY_FRONTEND);
    }

    public function getPrefix(): string
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_FRONTEND_PREFIX);
    }

    public function getHiddenClass(): string
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_FRONTEND_HIDDEN_CLASS);
    }

    public function getDisabledClass(): string
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_FRONTEND_DISABLED_CLASS);
    }

    public function getLoadingClass(): string
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_FRONTEND_LOADING_CLASS);
    }
}
