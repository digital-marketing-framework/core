<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\ConfigurationDocument\Discovery\StaticConfigurationDocumentDiscoveryInterface;

interface StaticConfigurationDocumentRegistryInterface
{
    public function addStaticConfigurationDocumentFolderIdentifier(string $identifier): void;

    /**
     * @return array<string>
     */
    public function getStaticConfigurationDocumentFolderIdentifiers(): array;

    public function registerStaticConfigurationDocumentDiscovery(StaticConfigurationDocumentDiscoveryInterface $discovery): void;

    /**
     * @return array<StaticConfigurationDocumentDiscoveryInterface>
     */
    public function getStaticConfigurationDocumentDiscoveries(): array;
}
