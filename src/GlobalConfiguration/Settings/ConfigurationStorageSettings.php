<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;

class ConfigurationStorageSettings extends GlobalSettings
{
    public function __construct()
    {
        parent::__construct('core', CoreGlobalConfigurationSchema::KEY_CONFIGURATION_STORAGE);
    }

    public function getConfigurationStorageFolder(): string
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_CONFIGURATION_STORAGE_FOLDER);
    }

    public function getDefaultConfigurationDocument(): string
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_CONFIGURATION_STORAGE_DEFAULT_DOCUMENT);
    }

    public function allowSaveToExtensionPaths(): bool
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_CONFIGURATION_STORAGE_ALLOW_SAVE_TO_EXTENSION_PATHS);
    }

    /**
     * @return array<string,string>
     */
    public function getDocumentAliases(): array
    {
        $aliasesString = trim((string)$this->get(CoreGlobalConfigurationSchema::KEY_CONFIGURATION_STORAGE_DOCUMENT_ALIASES));

        if ($aliasesString === '') {
            return [];
        }

        $aliases = [];
        $aliasPairStrings = explode(',', $aliasesString);
        foreach ($aliasPairStrings as $aliasPairString) {
            [$name, $path] = explode('=', $aliasPairString, 2);
            $aliases[trim($name)] = trim($path);
        }

        return $aliases;
    }
}
