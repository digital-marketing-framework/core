<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

use DigitalMarketingFramework\Core\DataSource\DataSourceManagerInterface;
use DigitalMarketingFramework\Core\Model\ConfigurationDocument\MigratableInterface;
use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface ConfigurationDocumentMaintenanceServiceInterface
{
    /**
     * Set the data source managers used to discover embedded configuration documents
     * (e.g. API endpoints, forms).
     *
     * @param array<DataSourceManagerInterface<DataSourceInterface>> $dataSourceManagers
     */
    public function setDataSourceManagers(array $dataSourceManagers): void;

    /**
     * Build a collection of all migratable configuration documents.
     *
     * Includes both storage-backed documents (from ConfigurationDocumentManager) and
     * embedded documents discovered via DataSourceManagers. Data source documents
     * are deduplicated by identifier.
     *
     * Each Migratable includes computed status (outdated, includes, includedBy, etc.).
     *
     * @return array<string, MigratableInterface> Keyed by document identifier
     */
    public function getAllMigratables(SchemaDocument $schemaDocument): array;

    /**
     * Migrate all outdated non-readonly documents in children-first order.
     *
     * @return array{
     *     migrated: array<string>,
     *     skipped: array<string>,
     *     failed: array<string, string>,
     * }
     */
    public function migrateAll(SchemaDocument $schemaDocument): array;

    /**
     * Migrate a single document (parents are migrated in-memory for context, not saved).
     *
     * @return bool Whether the document was actually migrated
     */
    public function migrateDocument(MigratableInterface $migratable, SchemaDocument $schemaDocument): bool;

    /**
     * Get the total count of deduplicated migratable identifiers.
     *
     * Lightweight operation — does not load full migratable objects or configuration documents.
     */
    public function getMigratableCount(): int;

    /**
     * Get a page of migratables with computed status (outdated, includes, includedBy).
     *
     * @return array<string, MigratableInterface> Keyed by document identifier
     */
    public function getMigratablePage(SchemaDocument $schemaDocument, int $offset, int $limit): array;

    /**
     * Get a single migratable by identifier with computed outdated status.
     */
    public function getMigratableByIdentifier(string $identifier, SchemaDocument $schemaDocument): ?MigratableInterface;

    /**
     * Mark a document's version mismatch as resolved by stamping all version tags
     * to the current schema versions without running any migrations.
     *
     * Used when a document has been manually fixed and just needs its version tags updated.
     */
    public function markAsResolved(MigratableInterface $migratable, SchemaDocument $schemaDocument): void;

    /**
     * Reset a variant migratable by copying the base migratable's configuration document.
     *
     * Only applicable to variant migratables (those with a base migratable identifier).
     * Replaces the variant's configuration document with the base's document.
     *
     * @return bool Whether the reset was performed (false if not a variant or base not found)
     */
    public function resetVariant(MigratableInterface $variant, SchemaDocument $schemaDocument): bool;
}
