<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

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

    /**
     * @return array<string>
     */
    public function getAllowedEnvironmentVariables(): array
    {
        $names = GeneralUtility::castValueToArray($this->get(CoreGlobalConfigurationSchema::KEY_ALLOWED_ENVIRONMENT_VARIABLES, ''));

        return array_values(array_filter($names, static fn (string $name): bool => $name !== ''));
    }
}
