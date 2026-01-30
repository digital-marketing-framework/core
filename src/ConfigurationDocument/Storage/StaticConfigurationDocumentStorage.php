<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

use BadMethodCallException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Discovery\StaticConfigurationDocumentDiscoveryInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class StaticConfigurationDocumentStorage extends ConfigurationDocumentStorage
{
    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    protected function getDiscovery(string $documentIdentifier): ?StaticConfigurationDocumentDiscoveryInterface
    {
        foreach ($this->registry->getStaticConfigurationDocumentDiscoveries() as $discovery) {
            if ($discovery->match($documentIdentifier)) {
                return $discovery;
            }
        }

        return null;
    }

    public function getDocumentIdentifiers(): array
    {
        $result = [];
        foreach ($this->registry->getStaticConfigurationDocumentDiscoveries() as $discovery) {
            $ids = $discovery->getIdentifiers();
            foreach ($ids as $id) {
                if (!in_array($id, $result, true)) {
                    $result[] = $id;
                }
            }
        }

        return $result;
    }

    public function getDocumentIdentifierFromBaseName(string $baseName, bool $newFile = true): string
    {
        throw new BadMethodCallException('Base name generation is not supported for static documents');
    }

    public function getDocument(string $documentIdentifier, bool $metaDataOnly = false): string
    {
        $discovery = $this->getDiscovery($documentIdentifier);

        return $discovery instanceof StaticConfigurationDocumentDiscoveryInterface
            ? $discovery->getContent($documentIdentifier, $metaDataOnly)
            : '';
    }

    public function setDocument(string $documentIdentifier, string $document): void
    {
        $this->getDiscovery($documentIdentifier)?->setContent($documentIdentifier, $document);
    }

    public function deleteDocument(string $documentIdentifier): void
    {
        throw new BadMethodCallException('Static documents cannot be deleted');
    }

    public function isReadOnly(string $documentIdentifier): bool
    {
        return $this->getDiscovery($documentIdentifier)?->isReadonly($documentIdentifier) ?? true;
    }
}
