<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Discovery;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;

class StaticAliasConfigurationDocumentDiscovery extends StaticResourceConfigurationDocumentDiscovery implements GlobalConfigurationAwareInterface
{
    use GlobalConfigurationAwareTrait;

    /** @var ?array<string,string> */
    protected ?array $aliases = null;

    /**
     * @return array<string,string>
     */
    protected function getAliases(): array
    {
        if ($this->aliases === null) {
            $aliases = $this->getConfigurationStorageSettings()->getDocumentAliases();
            $this->aliases = [];
            foreach ($aliases as $name => $path) {
                $this->aliases['ALS:' . $name] = $path;
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
        $identifiers = array_keys($this->getAliases());

        return array_filter($identifiers, function (string $identifier) {
            $exists = $this->exists($identifier);
            if (!$exists) {
                $this->logger->error(sprintf('Aliased configuration document path "%s" not found.', $identifier));
            }

            return $exists;
        });
    }

    public function match(string $identifier): bool
    {
        return $this->exists($identifier);
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
