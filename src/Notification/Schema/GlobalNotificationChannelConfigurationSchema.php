<?php

namespace DigitalMarketingFramework\Core\Notification;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\GlobalConfigurationSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

abstract class GlobalNotificationChannelConfigurationSchema extends GlobalConfigurationSchema
{
    public const KEY_ENABLED = 'enabled';

    public const DEFAULT_ENABLED = false;

    public const KEY_COMPONENTS = 'components';

    public const DEFAULT_COMPONENTS = '*';

    public const KEY_LEVELS = 'levels';

    public const DEFAULT_LEVELS = '*';

    public function __construct()
    {
        $this->addProperty(static::KEY_ENABLED, new BooleanSchema(static::DEFAULT_ENABLED));
        $this->addProperty(static::KEY_LEVELS, new StringSchema(static::DEFAULT_LEVELS));
        $this->addProperty(static::KEY_COMPONENTS, new StringSchema(static::DEFAULT_COMPONENTS));
    }
}
