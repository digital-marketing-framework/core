<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

interface ConfigurationDocumentStorageInterface
{
    public function getDocumentIdentifiers(): array;
    public function getDocumentIdentiferFromBaseName(string $baseName, bool $newFile = true): string;
    public function getShortIdentifier(string $documentIdentifier): string;
    public function getDocument(string $documentIdentifier): string;
    public function setDocument(string $documentIdentifier, string $document): void;
    public function deleteDocument(string $documentIdentifier): void;
    public function isReadOnly(string $identifier): bool;
    public function initalizeConfigurationDocumentStorage(): void;
}
