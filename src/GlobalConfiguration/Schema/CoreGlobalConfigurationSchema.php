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

    public const KEY_CONFIGURATION_STORAGE = 'configurationStorage';

    public const KEY_CONFIGURATION_STORAGE_FOLDER = 'folder';

    public const KEY_CONFIGURATION_STORAGE_DEFAULT_DOCUMENT = 'defaultConfigurationDocument';

    public const KEY_CONFIGURATION_STORAGE_ALLOW_SAVE_TO_EXTENSION_PATHS = 'allowSaveToExtensionPaths';

    public const DEFAULT_CONFIGURATION_STORAGE_ALLOW_SAVE_TO_EXTENSION_PATHS = false;

    public const KEY_API = 'api';

    public const KEY_API_ENABLED = 'enabled';

    public const DEFAULT_API_ENABLED = false;

    public const KEY_API_BASE_PATH = 'basePath';

    public const DEFAULT_API_BASE_PATH = 'digital-marketing-framework/api';

    protected ContainerSchema $configurationStorageSchema;

    protected ContainerSchema $apiSchema;

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

    public function __construct()
    {
        parent::__construct();

        $this->getRenderingDefinition()->setLabel('Core');

        $this->addProperty(static::KEY_DEBUG, new BooleanSchema(static::DEFAULT_DEBUG));

        $this->configurationStorageSchema = $this->getConfigurationStorageSchema();
        $this->addProperty(static::KEY_CONFIGURATION_STORAGE, $this->configurationStorageSchema);

        $this->apiSchema = $this->getApiSchema();
        $this->addProperty(static::KEY_API, $this->apiSchema);
    }
}
