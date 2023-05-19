<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

use BadMethodCallException;

abstract class StaticConfigurationDocumentStorage extends ConfigurationDocumentStorage
{
    public function setDocument(string $documentIdentifier, string $document): void
    {
        throw new BadMethodCallException('Static configuration document storages cannot write configuration documents');
    }

    public function deleteDocument(string $documentIdentifier): void
    {
        throw new BadMethodCallException('Static configuration document storages cannot delete configuration documents');
    }

    public function getDocumentIdentiferFromBaseName(string $baseName, bool $newFile = true): string
    {
        throw new BadMethodCallException('Static configuration document storages do not have document base names');
    }

    public function isReadOnly(string $documentIdentifier): bool
    {
        return true;
    }
}
