<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;

class ConfigurationStorageSettings extends GlobalSettings implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const BLOCKED_ALIASES = ['defaults', 'reset'];

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
            $name = trim($name);
            $path = trim($path);

            if ($path === '') {
                $this->logger->error('Empty configuration document alias path is not valid.');
                continue;
            }

            if ($name === '') {
                $this->logger->error('Empty configuration document alias is not valid.');
                continue;
            }

            if (in_array($name, static::BLOCKED_ALIASES)) {
                $this->logger->error(sprintf('Configuration document alias "%s" is not allowed.', $name));
                continue;
            }

            $aliases[$name] = $path;
        }

        return $aliases;
    }
}
