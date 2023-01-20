<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\ConfigurationDocument\Exception\ConfigurationDocumentNotFoundException;

abstract class FileConfigurationDocumentStorage extends ConfigurationDocumentStorage
{
    abstract public function getDocumentIdentifiers(): array;

    protected function getDocumentPath(string $documentIdentifier): string
    {
        return $documentIdentifier;
    }

    public function getDocument(string $documentIdentifier): string
    {
        $documentPath = $this->getDocumentPath($documentIdentifier);

        if (!file_exists($documentPath)) {
            throw new ConfigurationDocumentNotFoundException(sprintf('Configuration document file not found: %s', $documentPath));
        }

        return file_get_contents($documentPath);
    }

    public function setDocument(string $documentIdentifier, string $document): void
    {
        $documentPath = $this->getDocumentPath($documentIdentifier);
        file_put_contents($documentPath, $document);
    }

    public function isReadOnly(string $identifier): bool
    {
        $documentPath = $this->getDocumentPath($documentIdentifier);
        return !is_writable($documentPath);
    }

    public function initalizeConfigurationDocumentStorage(): void
    {
    }
}
