<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\Discovery\StaticConfigurationDocumentDiscoveryInterface;

trait StaticConfigurationDocumentRegistryTrait
{
    /** @var array<StaticConfigurationDocumentDiscoveryInterface> */
    protected array $staticConfigurationDocumentDiscoveries = [];

    /** @var array<string> */
    protected array $staticConfigurationDocumentFolderIdentifiers = [];

    public function addStaticConfigurationDocumentFolderIdentifier(string $identifier): void
    {
        if (!in_array($identifier, $this->staticConfigurationDocumentFolderIdentifiers, true)) {
            $this->staticConfigurationDocumentFolderIdentifiers[] = $identifier;
        }
    }

    public function getStaticConfigurationDocumentFolderIdentifiers(): array
    {
        return $this->staticConfigurationDocumentFolderIdentifiers;
    }

    public function registerStaticConfigurationDocumentDiscovery(StaticConfigurationDocumentDiscoveryInterface $discovery): void
    {
        if (!in_array($discovery, $this->staticConfigurationDocumentDiscoveries)) {
            $this->staticConfigurationDocumentDiscoveries[] = $discovery;
        }
    }

    public function getStaticConfigurationDocumentDiscoveries(): array
    {
        return $this->staticConfigurationDocumentDiscoveries;
    }
}
