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
        $defaultConfigurationDocumentSchema->getAllowedValues()->addValue('', '-- NONE --');
        $defaultConfigurationDocumentSchema->getAllowedValues()->addValueSet('document/all');
        $defaultConfigurationDocumentSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
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

    public function __construct()
    {
        parent::__construct();
        $this->getRenderingDefinition()->setLabel('General');

        $this->addProperty(static::KEY_DEBUG, new BooleanSchema(static::DEFAULT_DEBUG));

        $environmentSchema = new StringSchema(static::DEFAULT_ENVIRONMENT);
        $environmentSchema->getRenderingDefinition()->setLabel('Default Host');
        $environmentSchema->getRenderingDefinition()->setGeneralDescription('Host address to use for logging when running CLI commands.');
        $this->addProperty(static::KEY_ENVIRONMENT, $environmentSchema);

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
