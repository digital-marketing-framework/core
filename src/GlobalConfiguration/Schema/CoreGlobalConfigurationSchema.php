<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration\Schema;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class CoreGlobalConfigurationSchema extends GlobalConfigurationSchema
{
    public const KEY_DEBUG = 'debug';

    public const DEFAULT_DEBUG = false;

    public const KEY_ENVIRONMENT = 'environment';

    public const DEFAULT_ENVIRONMENT = '';

    public const KEY_DEFAULT_TIMEZONE = 'defaultTimezone';

    public const VALUE_TIMEZONE_SERVER = 'server';

    public const DEFAULT_TIMEZONE = self::VALUE_TIMEZONE_SERVER;

    public const KEY_CONFIGURATION_STORAGE = 'configurationStorage';

    public const KEY_CONFIGURATION_STORAGE_FOLDER = 'folder';

    public const KEY_CONFIGURATION_STORAGE_DEFAULT_DOCUMENT = 'defaultConfigurationDocument';

    public const KEY_CONFIGURATION_STORAGE_DOCUMENT_ALIASES = 'documentAliases';

    public const KEY_CONFIGURATION_STORAGE_ADDITIONAL_DOCUMENT_FOLDERS = 'additionalDocumentFolders';

    public const KEY_CONFIGURATION_STORAGE_ALLOW_SAVE_TO_EXTENSION_PATHS = 'allowSaveToExtensionPaths';

    public const DEFAULT_CONFIGURATION_STORAGE_ALLOW_SAVE_TO_EXTENSION_PATHS = false;

    public const KEY_API = 'api';

    public const KEY_API_ENABLED = 'enabled';

    public const DEFAULT_API_ENABLED = false;

    public const KEY_API_BASE_PATH = 'basePath';

    public const DEFAULT_API_BASE_PATH = 'digital-marketing-framework/api';

    public const KEY_DATA_PRIVACY = 'dataPrivacy';

    public const KEY_DATA_PRIVACY_ENABLE_UNREGULATED = 'enableUnregulated';

    public const DEFAULT_DATA_PRIVACY_ENABLE_UNREGULATED = false;

    public const KEY_NOTIFICATIONS = 'notifications';

    public const KEY_NOTIFICATIONS_ENABLED = 'enabled';

    public const DEFAULT_NOTIFICATIONS_ENABLED = false;

    protected ContainerSchema $configurationStorageSchema;

    protected ContainerSchema $apiSchema;

    protected ContainerSchema $dataPrivacySchema;

    protected ContainerSchema $notificationsSchema;

    public function getWeight(): int
    {
        return 50;
    }

    protected function getConfigurationStorageSchema(): ContainerSchema
    {
        $configurationStorageSchema = new ContainerSchema();
        // $configurationStorageSchema->getRenderingDefinition()->setNavigationItem(false);

        $configurationStorageSchema->addProperty(static::KEY_CONFIGURATION_STORAGE_FOLDER, new StringSchema());

        $defaultConfigurationDocumentSchema = new StringSchema();
        $defaultConfigurationDocumentSchema->getRenderingDefinition()->setGeneralDescription('Configuration document to automatically include in new embedded configurations (forms, API endpoints). New documents will inherit settings from this default.');
        // TODO the configuration folder (and document aliases) are configured in the global settings,
        //      which is why fetching all documents in the scope of building the global settings schema is a recursive endeavour,
        //      mainly because the defaults of the global settings are derived from the global settings schema
        // $defaultConfigurationDocumentSchema->getAllowedValues()->addValue('', '-- NONE --');
        // $defaultConfigurationDocumentSchema->getAllowedValues()->addValueSet('document/all');
        // $defaultConfigurationDocumentSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $configurationStorageSchema->addProperty(static::KEY_CONFIGURATION_STORAGE_DEFAULT_DOCUMENT, $defaultConfigurationDocumentSchema);

        $documentAliasesSchema = new StringSchema();
        $documentAliasesSchema->getRenderingDefinition()->setGeneralDescription('Configuration document aliases to override documents per environment. Use environment variables to inject different document paths for different systems. Example: main=@{ANYREL_MAIN_DOCUMENT_PATH},shop=@{ANYREL_SHOP_DOCUMENT_PATH}');
        $configurationStorageSchema->addProperty(static::KEY_CONFIGURATION_STORAGE_DOCUMENT_ALIASES, $documentAliasesSchema);

        $additionalDocumentFoldersSchema = new StringSchema();
        $additionalDocumentFoldersSchema->getRenderingDefinition()->setLabel('Additional document folders (comma-separated)');
        $configurationStorageSchema->addProperty(static::KEY_CONFIGURATION_STORAGE_ADDITIONAL_DOCUMENT_FOLDERS, $additionalDocumentFoldersSchema);

        $configurationStorageSchema->addProperty(
            static::KEY_CONFIGURATION_STORAGE_ALLOW_SAVE_TO_EXTENSION_PATHS,
            new BooleanSchema(static::DEFAULT_CONFIGURATION_STORAGE_ALLOW_SAVE_TO_EXTENSION_PATHS)
        );

        return $configurationStorageSchema;
    }

    protected function getApiSchema(): ContainerSchema
    {
        $apiSchema = new ContainerSchema();
        $apiSchema->getRenderingDefinition()->setLabel('API');
        // $apiSchema->getRenderingDefinition()->setNavigationItem(false);

        $apiSchema->addProperty(static::KEY_API_ENABLED, new BooleanSchema(static::DEFAULT_API_ENABLED));
        $apiSchema->addProperty(static::KEY_API_BASE_PATH, new StringSchema(static::DEFAULT_API_BASE_PATH));

        return $apiSchema;
    }

    protected function getDataPrivacySchema(): ContainerSchema
    {
        $dataPrivacySchema = new ContainerSchema();
        $dataPrivacySchema->getRenderingDefinition()->setLabel('Data Privacy');
        // $dataPrivacySchema->getRenderingDefinition()->setNavigationItem(false);

        $enableUnregulatedSchema = new BooleanSchema(static::DEFAULT_DATA_PRIVACY_ENABLE_UNREGULATED);
        $enableUnregulatedSchema->getRenderingDefinition()->setLabel('Enable unregulated Data Privacy Plugin');
        $dataPrivacySchema->addProperty(static::KEY_DATA_PRIVACY_ENABLE_UNREGULATED, $enableUnregulatedSchema);

        return $dataPrivacySchema;
    }

    protected function getNotificationsSchema(): ContainerSchema
    {
        $notificationsSchema = new ContainerSchema();

        $notificationsSchema->getRenderingDefinition()->setLabel('Notifications');

        $enableNotificationsSchema = new BooleanSchema(static::DEFAULT_NOTIFICATIONS_ENABLED);
        $enableNotificationsSchema->getRenderingDefinition()->setLabel('Enable notifications');
        $notificationsSchema->addProperty(static::KEY_NOTIFICATIONS_ENABLED, $enableNotificationsSchema);

        return $notificationsSchema;
    }

    /**
     * Curated list of timezones with human-readable labels.
     * One representative per unique UTC offset, using major recognizable cities.
     *
     * @return array<string,string> timezone identifier => label
     */
    public static function getTimezoneOptions(): array
    {
        return [
            'Pacific/Midway' => 'UTC-11 Pacific/Midway (SST)',
            'Pacific/Honolulu' => 'UTC-10 Pacific/Honolulu (HST)',
            'America/Anchorage' => 'UTC-9 America/Anchorage (AKST)',
            'America/Los_Angeles' => 'UTC-8 America/Los Angeles (PST)',
            'America/Denver' => 'UTC-7 America/Denver (MST)',
            'America/Chicago' => 'UTC-6 America/Chicago (CST)',
            'America/New_York' => 'UTC-5 America/New York (EST)',
            'America/Halifax' => 'UTC-4 America/Halifax (AST)',
            'America/Sao_Paulo' => 'UTC-3 America/São Paulo',
            'Atlantic/South_Georgia' => 'UTC-2 Atlantic/South Georgia',
            'Atlantic/Azores' => 'UTC-1 Atlantic/Azores',
            'UTC' => 'UTC+0 UTC',
            'Europe/London' => 'UTC+0 Europe/London (GMT)',
            'Europe/Berlin' => 'UTC+1 Europe/Berlin (CET)',
            'Africa/Cairo' => 'UTC+2 Africa/Cairo (EET)',
            'Europe/Moscow' => 'UTC+3 Europe/Moscow (MSK)',
            'Asia/Tehran' => 'UTC+3:30 Asia/Tehran',
            'Asia/Dubai' => 'UTC+4 Asia/Dubai',
            'Asia/Kabul' => 'UTC+4:30 Asia/Kabul',
            'Asia/Karachi' => 'UTC+5 Asia/Karachi (PKT)',
            'Asia/Kolkata' => 'UTC+5:30 Asia/Kolkata (IST)',
            'Asia/Kathmandu' => 'UTC+5:45 Asia/Kathmandu',
            'Asia/Dhaka' => 'UTC+6 Asia/Dhaka',
            'Asia/Yangon' => 'UTC+6:30 Asia/Yangon',
            'Asia/Bangkok' => 'UTC+7 Asia/Bangkok',
            'Asia/Singapore' => 'UTC+8 Asia/Singapore',
            'Asia/Tokyo' => 'UTC+9 Asia/Tokyo (JST)',
            'Australia/Darwin' => 'UTC+9:30 Australia/Darwin (ACST)',
            'Australia/Sydney' => 'UTC+10 Australia/Sydney (AEST)',
            'Pacific/Noumea' => 'UTC+11 Pacific/Noumea',
            'Pacific/Auckland' => 'UTC+12 Pacific/Auckland (NZST)',
            'Pacific/Tongatapu' => 'UTC+13 Pacific/Tongatapu',
        ];
    }

    protected function getTimezoneSchema(): StringSchema
    {
        $timezoneSchema = new StringSchema(static::DEFAULT_TIMEZONE);
        $timezoneSchema->getRenderingDefinition()->setLabel('Default Timezone');
        $timezoneSchema->getRenderingDefinition()->setGeneralDescription('Timezone used for date/time values. "Server" uses the server\'s configured timezone.');
        $timezoneSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);

        // Add special "server default" option with current server timezone in label
        $serverTimezone = date_default_timezone_get();
        $timezoneSchema->getAllowedValues()->addValue(static::VALUE_TIMEZONE_SERVER, 'Server Default (' . $serverTimezone . ')');

        // Add curated timezone options
        foreach (static::getTimezoneOptions() as $identifier => $label) {
            $timezoneSchema->getAllowedValues()->addValue($identifier, $label);
        }

        return $timezoneSchema;
    }

    public function __construct()
    {
        parent::__construct();
        $this->getRenderingDefinition()->setLabel('General');

        $this->addProperty(static::KEY_DEBUG, new BooleanSchema(static::DEFAULT_DEBUG));

        $environmentSchema = new StringSchema(static::DEFAULT_ENVIRONMENT);
        $environmentSchema->getRenderingDefinition()->setLabel('Default Host');
        $environmentSchema->getRenderingDefinition()->setGeneralDescription('Host address to use for logging when running CLI commands.');
        $this->addProperty(static::KEY_ENVIRONMENT, $environmentSchema);

        $this->addProperty(static::KEY_DEFAULT_TIMEZONE, $this->getTimezoneSchema());

        $this->configurationStorageSchema = $this->getConfigurationStorageSchema();
        $this->addProperty(static::KEY_CONFIGURATION_STORAGE, $this->configurationStorageSchema);

        $this->apiSchema = $this->getApiSchema();
        $this->addProperty(static::KEY_API, $this->apiSchema);

        $this->dataPrivacySchema = $this->getDataPrivacySchema();
        $this->addProperty(static::KEY_DATA_PRIVACY, $this->dataPrivacySchema);

        $this->notificationsSchema = $this->getNotificationsSchema();
        $this->addProperty(static::KEY_NOTIFICATIONS, $this->notificationsSchema);
    }
}
