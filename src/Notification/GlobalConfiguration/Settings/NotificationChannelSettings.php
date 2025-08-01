<?php

namespace DigitalMarketingFramework\Core\Notification\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\GlobalSettings;
use DigitalMarketingFramework\Core\Notification\GlobalConfiguration\Schema\NotificationChannelSchema;

abstract class NotificationChannelSettings extends GlobalSettings
{
    public function enabled(): bool
    {
        return $this->get(NotificationChannelSchema::KEY_ENABLED);
    }

    public function getComponents(): string
    {
        return $this->get(NotificationChannelSchema::KEY_COMPONENTS);
    }

    public function getLevels(): string
    {
        return $this->get(NotificationChannelSchema::KEY_LEVELS);
    }
}
