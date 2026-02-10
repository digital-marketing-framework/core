<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Migration;

use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface ConfigurationDocumentMigrationServiceInterface
{
    public function addMigration(ConfigurationDocumentMigrationInterface $migration): void;

    /**
     * Check if a configuration is outdated compared to the current schema version.
     * This is a quick check based on version tags only.
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
    public function genuinelyOutdated(array $configuration, MigrationContext $context, SchemaDocument $schemaDocument): bool;

    /**
     * Migrate a single configuration delta with the given context.
     *
     * @param array<string,mixed> $delta
     *
     * @return array<string,mixed>
     */
    public function migrateConfiguration(array $delta, MigrationContext $context, SchemaDocument $schemaDocument): array;

    /**
     * Migrate an entire configuration stack in memory.
     * Each entry is migrated to the target version with correct parent context.
     * No documents are saved — this is for on-the-fly migration.
     *
     * @param array<array<string,mixed>> $stack Configuration stack (first entry = root/SYS:defaults, last = leaf)
     * @param array<string,string> $targetVersion Per-key target versions from SchemaDocument
     */
    public function migrateStackInMemory(array &$stack, array $targetVersion): void;
}
