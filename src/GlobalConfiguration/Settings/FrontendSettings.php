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

    public function getFrameAllowedOrigins(): string
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_FRONTEND_FRAME_ALLOWED_ORIGINS);
    }

    public function getFrameTimeout(): int
    {
        return (int)$this->get(CoreGlobalConfigurationSchema::KEY_FRONTEND_FRAME_TIMEOUT);
    }

    public function getFrameAutoResizeParam(): string
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_FRONTEND_FRAME_AUTO_RESIZE_PARAM);
    }

    public function getFrameMeasuringClass(): string
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_FRONTEND_FRAME_MEASURING_CLASS);
    }
}
