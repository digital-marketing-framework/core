<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationServiceInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;
use DigitalMarketingFramework\Core\Model\ConfigurationDocument\ConfigurationDocumentInformation;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface ConfigurationDocumentManagerInterface
{
    /** @var string */
    public const KEY_META_DATA = 'metaData';

    /** @var string */
    public const KEY_INCLUDES = 'includes';

    /** @var string */
    public const KEY_DOCUMENT_NAME = 'name';

    /** @var string */
    public const KEY_DOCUMENT_STRICT_VALIDATION = 'strictValidation';

    /** @var string */
    public const KEY_DOCUMENT_VERSION = 'version';

    public function getStorage(): ConfigurationDocumentStorageInterface;

    public function getParser(): ConfigurationDocumentParserInterface;

    public function getStaticStorage(): ConfigurationDocumentStorageInterface;

    public function getMigrationService(): ConfigurationDocumentMigrationServiceInterface;

    public function tidyDocument(string $document, SchemaDocument $schemaDocument): string;

    public function saveDocument(string $documentIdentifier, string $document, SchemaDocument $schemaDocument): void;

    public function createDocument(string $documentIdentifier, string $document, string $documentName, SchemaDocument $schemaDocument): void;

    public function deleteDocument(string $documentIdentifier): void;

    public function getDocumentIdentifierFromBaseName(string $baseName, bool $newFile = true): string;

    public function getDocumentInformation(string $documentIdentifier): ConfigurationDocumentInformation;

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
     * Quick check if a configuration is outdated based on version tags.
     *
     * @param array<string,mixed> $configuration
     */
    public function outdated(array $configuration, SchemaDocument $schemaDocument): bool;

    /**
     * Check if a configuration is genuinely outdated — meaning migration would
     * actually change the configuration data, not just the version tags.
     *
     * This is slower than outdated() because it runs the actual migration and
     * compares before/after. Use this when you need to know if a document
     * truly needs migration (e.g., to decide whether to show a warning in the editor).
     *
     * @param array<string,mixed> $configuration
     */
    public function genuinelyOutdated(array $configuration, SchemaDocument $schemaDocument): bool;

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
     * Build the configuration stack from a configuration array.
     *
     * @param array<mixed> $configuration
     * @param SchemaDocument|null $schemaDocument If provided and $migrateInMemory is true, the stack will be migrated in memory
     * @param bool $migrateInMemory Whether to migrate the stack in memory (requires $schemaDocument)
     *
     * @return array<array<mixed>>
     */
    public function getConfigurationStackFromConfiguration(array $configuration, ?SchemaDocument $schemaDocument = null, bool $migrateInMemory = true): array;

    /**
     * Build the configuration stack from a document string.
     *
     * @param SchemaDocument|null $schemaDocument If provided and $migrateInMemory is true, the stack will be migrated in memory
     * @param bool $migrateInMemory Whether to migrate the stack in memory (requires $schemaDocument)
     *
     * @return array<array<string,mixed>>
     */
    public function getConfigurationStackFromDocument(string $document, ?SchemaDocument $schemaDocument = null, bool $migrateInMemory = true): array;

    /**
     * Build the configuration stack from a document identifier.
     *
     * @param SchemaDocument|null $schemaDocument If provided and $migrateInMemory is true, the stack will be migrated in memory
     * @param bool $migrateInMemory Whether to migrate the stack in memory (requires $schemaDocument)
     *
     * @return array<array<string,mixed>>
     */
    public function getConfigurationStackFromIdentifier(string $documentIdentifier, ?SchemaDocument $schemaDocument = null, bool $migrateInMemory = true): array;

    public function getDefaultConfigurationIdentifier(): string;

    /**
     * Build the configuration stack from the default document.
     *
     * @param SchemaDocument|null $schemaDocument If provided and $migrateInMemory is true, the stack will be migrated in memory
     * @param bool $migrateInMemory Whether to migrate the stack in memory (requires $schemaDocument)
     *
     * @return array<array<string,mixed>>
     */
    public function getDefaultConfigurationStack(?SchemaDocument $schemaDocument = null, bool $migrateInMemory = true): array;
}
