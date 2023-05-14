<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;

interface ConfigurationDocumentManagerInterface
{
    public const KEY_INCLUDES = 'includes';

    public function getStorage(): ConfigurationDocumentStorageInterface;
    public function getParser(): ConfigurationDocumentParserInterface;
    public function getStaticStorage(): ?ConfigurationDocumentStorageInterface;

    public function tidyDocument(string $document): string;

    public function saveDocument(string $documentIdentifier, string $document): void;

    /**
     * @return array<string>
     */
    public function getDocumentIdentifiers(): array;

    /**
     * @return array<mixed>
     */
    public function getDocumentConfigurationFromDocument(string $document): array;

    public function getDocumentFromIdentifier(string $documentIdentifier): string;

    /**
     * @return array<mixed>
     */
    public function getDocumentConfigurationFromIdentifier(string $documentIdentifier): array;

    /**
     * @param array<mixed> $configuration
     * @return array<array<mixed>>
     */
    public function getConfigurationStackFromConfiguration(array $configuration): array;

    /**
     * @return array<array<mixed>>
     */
    public function getConfigurationStackFromDocument(string $document): array;

    /**
     * @return array<array<mixed>>
     */
    public function getConfigurationStackFromIdentifier(string $documentIdentifier): array;
}
