<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;

interface ConfigurationDocumentManagerInterface
{
    public const KEY_META_DATA = 'metaData';

    public const KEY_INCLUDES = 'includes';

    public const KEY_DOCUMENT_NAME = 'name';

    public const KEY_DOCUMENT_VERSION = 'version';

    public function getStorage(): ConfigurationDocumentStorageInterface;

    public function getParser(): ConfigurationDocumentParserInterface;

    public function getStaticStorage(): ?ConfigurationDocumentStorageInterface;

    public function tidyDocument(string $document, SchemaDocument $schemaDocument): string;

    public function saveDocument(string $documentIdentifier, string $document, SchemaDocument $schemaDocument): void;

    public function createDocument(string $documentIdentifier, string $document, string $documentName, SchemaDocument $schemaDocument): void;

    public function deleteDocument(string $documentIdentifier): void;

    public function getDocumentIdentifierFromBaseName(string $baseName, bool $newFile = true): string;

    /**
     * @return array{id:string,shortId:string,name:string,readonly:bool,includes:array<string>}
     */
    public function getDocumentInformation(string $documentIdentifier): array;

    /**
     * @param array<string,mixed> $configuration
     *
     * @return array<string>
     */
    public function getIncludes(array $configuration): array;

    /**
     * @param array<string,mixed> $configuration
     * @param array<string> $includes
     */
    public function setIncludes(array &$configuration, array $includes): void;

    /**
     * @param array<string,mixed> $configuration
     */
    public function getName(array $configuration): string;

    /**
     * @param array<string,mixed> $configuration
     */
    public function setName(array &$configuration, string $name): void;

    /**
     * @param array<string,mixed> $configuration
     *
     * @return array<string,string>
     */
    public function getVersion(array $configuration): array;

    /**
     * @param array<string,mixed> $configuration
     * @param array<string,string> $version
     */
    public function setVersion(array &$configuration, array $version): void;

    /**
     * @param array<string,mixed> $configuration
     */
    public function getVersionByKey(array $configuration, string $key): string;

    /**
     * @param array<string,mixed> $configuration
     */
    public function setVersionByKey(array &$configuration, string $key, string $version): void;

    /**
     * @param array<string,mixed> $configuration
     */
    public function unsetVersionByKey(array &$configuration, string $key): void;

    /**
     * @param array<string,mixed> $configuration
     */
    public function outdated(array $configuration, SchemaDocument $schemaDocument): bool;

    public function addMigration(ConfigurationDocumentMigrationInterface $migration): void;

    /**
     * @param array<string,mixed> $configuration
     *
     * @return array<string,mixed>
     */
    public function migrate(array $configuration, SchemaDocument $schemaDocument): array;

    /**
     * @return array<string>
     */
    public function getDocumentIdentifiers(): array;

    /**
     * @return array<string,mixed>
     */
    public function getDocumentConfigurationFromDocument(string $document): array;

    public function getDocumentFromIdentifier(string $documentIdentifier, bool $metaDataOnly = false): string;

    /**
     * @return array<string,mixed>
     */
    public function getDocumentConfigurationFromIdentifier(string $documentIdentifier, bool $metaDataOnly = false): array;

    /**
     * @param array<mixed> $configuration
     *
     * @return array<array<mixed>>
     */
    public function getConfigurationStackFromConfiguration(array $configuration): array;

    /**
     * @return array<array<string,mixed>>
     */
    public function getConfigurationStackFromDocument(string $document): array;

    /**
     * @return array<array<string,mixed>>
     */
    public function getConfigurationStackFromIdentifier(string $documentIdentifier): array;

    /**
     * @param array<string,mixed> $mergedConfiguration
     *
     * @return array<string,mixed>
     */
    public function splitConfiguration(array $mergedConfiguration): array;

    /**
     * @param array<string,mixed> $configuration
     *
     * @return array<string,mixed>
     */
    public function mergeConfiguration(array $configuration, bool $inheritedConfigurationOnly = false): array;

    /**
     * @param array<string,mixed> $referenceMergedConfiguration
     * @param array<string,mixed> $mergedConfiguration
     *
     * @return array<string,mixed>
     */
    public function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array;
}
