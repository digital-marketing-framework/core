<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

interface ConfigurationDocumentStorageInterface
{
    public function getDocumentIdentifiers(): array;
    public function getDocument(string $documentIdentifier): string;
    public function setDocument(string $documentIdentifier, string $document): void;
}
