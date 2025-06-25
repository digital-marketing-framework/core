<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Discovery;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\ConfigurationStorageSettings;

class StaticAliasConfigurationDocumentDiscovery extends StaticResourceConfigurationDocumentDiscovery implements GlobalConfigurationAwareInterface
{
    use GlobalConfigurationAwareTrait;

    protected ConfigurationStorageSettings $configurationStorageSettings;

    /** @var ?array<string,string> */
    protected ?array $aliases = null;

    protected function getConfigurationStorageSettings(): ConfigurationStorageSettings
    {
        if (!isset($this->configurationStorageSettings)) {
            $this->configurationStorageSettings = $this->globalConfiguration->getGlobalSettings(ConfigurationStorageSettings::class);
        }

        return $this->configurationStorageSettings;
    }

    /**
     * @return array<string,string>
     */
    protected function getAliases(): array
    {
        if ($this->aliases === null) {
            $aliases = $this->getConfigurationStorageSettings()->getDocumentAliases();
            $this->aliases = [];
            foreach ($aliases as $name => $path) {
                $this->aliases['SYS:' . $name] = $path;
            }
        }

        return $this->aliases;
    }

    protected function resolveAlias(string $identifier): ?string
    {
        return $this->getAliases()[$identifier] ?? null;
    }

    public function getIdentifiers(): array
    {
        return array_keys($this->getAliases());
    }

    public function match(string $identifier): bool
    {
        return $this->resolveAlias($identifier) !== null;
    }

    public function exists(string $identifier): bool
    {
        $target = $this->resolveAlias($identifier);
        if ($target === null) {
            return false;
        }

        return parent::exists($target);
    }

    public function isReadonly(string $identifier): bool
    {
        return true;
    }

    public function getContent(string $identifier, bool $metaDataOnly = false): ?string
    {
        $target = $this->resolveAlias($identifier);
        if ($target !== null) {
            return parent::getContent($target, $metaDataOnly);
        }

        return null;
    }
}
