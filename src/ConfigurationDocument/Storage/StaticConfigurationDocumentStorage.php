<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

use BadMethodCallException;

abstract class StaticConfigurationDocumentStorage extends ConfigurationDocumentStorage
{
    public function setDocument(string $documentIdentifier, string $document): void
    {
        throw new BadMethodCallException('Static configuration document storages cannot write configuration documents');
    }

    public function isReadOnly(string $documentIdentifier): bool
    {
        return true;
    }
}
