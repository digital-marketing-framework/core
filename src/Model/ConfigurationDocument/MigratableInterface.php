<?php

namespace DigitalMarketingFramework\Core\Model\ConfigurationDocument;

use DigitalMarketingFramework\Core\Model\ItemInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

/**
 * Represents a configuration document that can be migrated.
 *
 * Combines document access (read/write) with computed migration status.
 * Implementations exist for storage-backed documents and data source documents.
 */
interface MigratableInterface extends ItemInterface
{
    // -- Identity --

    public function getIdentifier(): string;

    public function getName(): string;

    public function isReadOnly(): bool;

    /**
     * Source type: 'storage' for central configs, 'dataSource' for embedded docs.
     */
    public function getSource(): string;

    /**
     * Group key for sectioned display in the backend maintenance UI.
     * E.g. 'storage', 'api', 'form'.
     */
    public function getMigratableGroup(): string;

    /**
     * Short description for display in the backend UI.
     */
    public function getDescription(): string;

    /**
     * Whether this migratable type supports variants (e.g. form plugin overrides).
     */
    public function canHaveVariants(): bool;

    /**
     * For variant migratables, returns the identifier of the base (original) migratable.
     * Returns null if this is already a base migratable.
     */
    public function getBaseMigratableIdentifier(): ?string;

    /**
     * Whether this is a variant of a base migratable (e.g. a form plugin override).
     */
    public function isVariant(): bool;

    // -- Document I/O --

    public function getConfigurationDocument(): string;

    public function saveConfigurationDocument(string $document, SchemaDocument $schemaDocument): void;

    // -- Relationships --

    /**
     * @return array<string> Identifiers of parent documents this one includes
     */
    public function getIncludes(): array;

    /**
     * @param array<string> $includes
     */
    public function setIncludes(array $includes): void;

    /**
     * @return array<string> Identifiers of child documents that include this one
     */
    public function getIncludedBy(): array;

    /**
     * @param array<string> $includedBy
     */
    public function setIncludedBy(array $includedBy): void;

    // -- Migration status (computed by maintenance service) --

    /**
     * Whether the configuration document is empty (no content).
     * Empty documents skip migration checks and show a distinct status.
     */
    public function isEmpty(): bool;

    public function setEmpty(bool $empty): void;

    public function isOutdated(): bool;

    public function setOutdated(bool $outdated): void;

    public function hasOutdatedParents(): bool;

    public function setHasOutdatedParents(bool $hasOutdatedParents): void;

    /**
     * Whether this document can be migrated individually.
     * False when child documents (that include this one) are still outdated.
     */
    public function canMigrateIndividually(): bool;

    public function setCanMigrateIndividually(bool $canMigrateIndividually): void;

    /**
     * Migration details per package: version differences and migration status.
     *
     * Status values (see MigrationContext constants):
     *   'tagOnly'  — version mismatch, no data changes (yellow)
     *   'genuine'  — migration would make real data changes (orange)
     *   'error'    — migration failed with a fatal error (red)
     *
     * @return array<string, array{from: string, to: string, status: string, message: string}>
     */
    public function getMigrationInfo(): array;

    /**
     * @param array<string, array{from: string, to: string, status: string, message: string}> $migrationInfo
     */
    public function setMigrationInfo(array $migrationInfo): void;
}
