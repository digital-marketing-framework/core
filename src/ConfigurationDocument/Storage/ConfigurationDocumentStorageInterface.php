<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

interface ConfigurationDocumentStorageInterface
{
    /**
     * @return array<string>
     */
    public function getDocumentIdentifiers(): array;

    public function getDocumentIdentifierFromBaseName(string $baseName, bool $newFile = true): string;

    public function getShortIdentifier(string $documentIdentifier): string;

    public function getDocument(string $documentIdentifier, bool $metaDataOnly = false): string;

    public function setDocument(string $documentIdentifier, string $document): void;

    public function deleteDocument(string $documentIdentifier): void;

    public function isReadOnly(string $documentIdentifier): bool;

    public function initializeConfigurationDocumentStorage(): void;

    /**
     * Whether a document could be stored, which is not the same as the folder being there:
     * the folder appears with the first document.
     */
    public function isStorageReady(): bool;

    /**
     * Whether the storage folder can be fetched over the web.
     */
    public function isStoragePubliclyAccessible(): bool;

    /**
     * Whether it is in fact unreachable — either because the storage is not public, or because
     * an access file is in place and this server reads them.
     */
    public function isStorageProtected(): bool;
}
