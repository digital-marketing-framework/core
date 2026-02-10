<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Migration;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class ConfigurationDocumentMigrationService implements ConfigurationDocumentMigrationServiceInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected const IMPLICIT_VERSION = SchemaDocument::INITIAL_VERSION;

    /** @var array<string,array<string,ConfigurationDocumentMigrationInterface>> */
    protected array $migrations = [];

    public function addMigration(ConfigurationDocumentMigrationInterface $migration): void
    {
        $this->migrations[$migration->getKey()][$migration->getSourceVersion()] = $migration;
    }

    /**
     * @param array<string,mixed> $configuration
     */
    protected function getVersionByKey(array $configuration, string $key): string
    {
        return $configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION][$key] ?? '';
    }

    /**
     * @param array<string,mixed> $configuration
     */
    protected function setVersionByKey(array &$configuration, string $key, string $version): void
    {
        $configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION][$key] = $version;
    }

    /**
     * @param array<string,mixed> $configuration
     *
     * @return array<string,string>
     */
    protected function getVersion(array $configuration): array
    {
        return $configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION] ?? [];
    }

    /**
     * Get the effective version for a key, treating empty/missing as the implicit baseline.
     *
     * @param array<string, mixed> $configuration
     */
    protected function getEffectiveVersion(array $configuration, string $key): string
    {
        $version = $this->getVersionByKey($configuration, $key);

        return $version !== '' ? $version : static::IMPLICIT_VERSION;
    }

    public function outdated(array $configuration, SchemaDocument $schemaDocument): bool
    {
        $schemaVersion = $schemaDocument->getVersion();
        foreach ($schemaVersion as $key => $targetVersion) {
            if ($this->getVersionByKey($configuration, $key) !== $targetVersion) {
                return true;
            }
        }

        // NOTE: Orphan version tags (from uninstalled packages) are not considered "outdated".
        // They are harmless (data becomes inert) and there is no migration path for removal.
        // Cleanup of orphan tags can happen opportunistically during actual migrations.

        return false;
    }

    /**
     * Migrate a configuration for a single key through the migration chain.
     * This is the inner loop — it chains migration steps from source to target version.
     *
     * @param array<string,mixed> $configuration
     * @param string $targetVersion The version to migrate to
     */
    protected function migrateConfigurationByKey(array &$configuration, MigrationContext $context, string $key, string $targetVersion): void
    {
        $fromVersion = $this->getVersionByKey($configuration, $key);
        $before = $this->stripVersionTags($configuration);
        $version = $this->getEffectiveVersion($configuration, $key);

        while ($version !== $targetVersion) {
            $migration = $this->migrations[$key][$version] ?? null;
            if ($migration === null) {
                break;
            }

            if (!$migration->checkVersions()) {
                $exception = new FatalMigrationException(sprintf('Migration source version "%s" seems to be bigger than or equal to target version "%s".', $migration->getSourceVersion(), $migration->getTargetVersion()));
                $context->recordMigrationResult($key, $fromVersion, $targetVersion, MigrationContext::STATUS_ERROR, $exception->getMessage());

                throw $exception;
            }

            try {
                $configuration = $migration->migrate($configuration, $context);
                $version = $migration->getTargetVersion();
            } catch (FatalMigrationException $e) {
                $context->recordMigrationResult($key, $fromVersion, $targetVersion, MigrationContext::STATUS_ERROR, $e->getMessage());

                throw $e;
            } catch (MigrationException $e) {
                $this->logger->warning($e->getMessage());
                break;
            }
        }

        $this->setVersionByKey($configuration, $key, $version);

        $status = $this->stripVersionTags($configuration) !== $before
            ? MigrationContext::STATUS_GENUINE
            : MigrationContext::STATUS_TAG_ONLY;
        $context->recordMigrationResult($key, $fromVersion, $targetVersion, $status);
    }

    /**
     * Option C iterative: ensure no parent in the stack is at a version lower
     * than the document after it (its "child" in the stack) for the given key.
     *
     * Two-pass algorithm:
     * 1. Bottom-up: calculate required version for each parent
     * 2. Top-down: migrate each parent to its required version
     *
     * @param array<array<string,mixed>> $parentStack
     * @param array<string,mixed> $sysDefaults
     * @param string $key The migration key
     * @param string $leafVersion The current version of the leaf document
     */
    protected function ensureParentVersionsForKey(array &$parentStack, array $sysDefaults, string $key, string $leafVersion): void
    {
        if ($parentStack === []) {
            return;
        }

        // Pass 1: Bottom-up — calculate required version for each parent
        $requiredVersions = [];
        $minRequired = $leafVersion;

        for ($i = count($parentStack) - 1; $i >= 0; --$i) {
            $currentVersion = $this->getEffectiveVersion($parentStack[$i], $key);

            // Parent must be at least minRequired (from the child below it)
            // If parent is already ahead, use its current version (propagate upward)
            if (version_compare($currentVersion, $minRequired, '>=')) {
                $requiredVersions[$i] = $currentVersion;
                $minRequired = $currentVersion;
            } else {
                $requiredVersions[$i] = $minRequired;
                // minRequired stays the same — propagate upward
            }
        }

        $counter = count($parentStack);

        // Pass 2: Top-down — migrate parents to their required versions
        for ($i = 0; $i < $counter; ++$i) {
            $currentVersion = $this->getEffectiveVersion($parentStack[$i], $key);
            $targetVersion = $requiredVersions[$i];

            if (version_compare($currentVersion, $targetVersion, '<')) {
                $parentContext = new MigrationContext(
                    array_slice($parentStack, 0, $i),
                    $sysDefaults
                );
                $this->migrateConfigurationByKey($parentStack[$i], $parentContext, $key, $targetVersion);
            }
        }
    }

    /**
     * Migrate a leaf document's delta through all keys, with Option C iterative
     * parent version management at each migration step.
     *
     * @param array<string,mixed> $delta
     * @param array<array<string,mixed>> $parentStack
     * @param array<string,mixed> $sysDefaults
     */
    protected function migrateByKey(array &$delta, array &$parentStack, array $sysDefaults, string $key, string $targetVersion): void
    {
        $version = $this->getEffectiveVersion($delta, $key);

        while ($version !== $targetVersion) {
            $migration = $this->migrations[$key][$version] ?? null;
            if ($migration === null) {
                break;
            }

            if (!$migration->checkVersions()) {
                throw new FatalMigrationException(sprintf('Migration source version "%s" seems to be bigger than or equal to target version "%s".', $migration->getSourceVersion(), $migration->getTargetVersion()));
            }

            // Option C iterative: ensure parent versions are consistent for this step
            $this->ensureParentVersionsForKey($parentStack, $sysDefaults, $key, $version);

            $context = new MigrationContext($parentStack, $sysDefaults);

            try {
                $delta = $migration->migrate($delta, $context);
                $version = $migration->getTargetVersion();
            } catch (MigrationException $e) {
                $this->logger->warning($e->getMessage());
                break;
            }
        }

        $this->setVersionByKey($delta, $key, $version);
    }

    public function migrateConfiguration(array $delta, MigrationContext $context, SchemaDocument $schemaDocument): array
    {
        // For the simple case (no parent stack management), use the direct migration path.
        // This is used by the ConfigurationDocumentManager facade.
        $schemaVersion = $schemaDocument->getVersion();
        foreach ($schemaVersion as $key => $targetVersion) {
            if ($this->getVersionByKey($delta, $key) !== $targetVersion) {
                $this->migrateConfigurationByKey($delta, $context, $key, $targetVersion);
            }
        }

        // NOTE: Orphan version tags (from uninstalled packages) are left in place.
        // They are harmless and there is no migration path for their removal.

        return $delta;
    }

    /**
     * Strip version tags from configuration for comparison purposes.
     *
     * @param array<string,mixed> $configuration
     *
     * @return array<string,mixed>
     */
    protected function stripVersionTags(array $configuration): array
    {
        unset($configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]);

        // Clean up empty metaData array if version was the only key
        if (isset($configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA])
            && $configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA] === []
        ) {
            unset($configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA]);
        }

        return $configuration;
    }

    public function genuinelyOutdated(array $configuration, MigrationContext $context, SchemaDocument $schemaDocument): bool
    {
        // Quick check first: if version tags match, definitely not outdated
        if (!$this->outdated($configuration, $schemaDocument)) {
            return false;
        }

        // Run the migration — per-key results are tracked on the context
        $this->migrateConfiguration($configuration, $context, $schemaDocument);

        return $context->hasGenuineChanges();
    }

    public function migrateStackInMemory(array &$stack, array $targetVersion): void
    {
        if (count($stack) < 2) {
            return;
        }

        // First entry is SYS:defaults — always current, not migrated
        $sysDefaults = $stack[0];
        $counter = count($stack);

        // Migrate entries in children-first order (from leaf toward root).
        // This ensures that when we process a document, its parents are still
        // at their original versions. ensureParentVersionsForKey brings parents
        // up to the document's current version (not beyond), and the write-back
        // updates the stack. When we later process a parent, it continues from
        // wherever it was left by earlier children.
        for ($i = $counter - 1; $i >= 1; --$i) {
            $outdated = false;
            foreach ($targetVersion as $key => $version) {
                if ($this->getVersionByKey($stack[$i], $key) !== $version) {
                    $outdated = true;
                    break;
                }
            }

            if (!$outdated) {
                continue;
            }

            // Build parent stack for this entry (entries 1..i-1, excluding SYS:defaults)
            $parentStack = array_slice($stack, 1, $i - 1);

            foreach ($targetVersion as $key => $version) {
                if ($this->getVersionByKey($stack[$i], $key) !== $version) {
                    $this->migrateByKey($stack[$i], $parentStack, $sysDefaults, $key, $version);
                }
            }

            // Write back any parent changes from ensureParentVersionsForKey
            $parentStackCount = count($parentStack);
            for ($j = 0; $j < $parentStackCount; ++$j) {
                $stack[$j + 1] = $parentStack[$j];
            }

            // NOTE: Orphan version tags are left in place (see outdated() comment).
        }
    }
}
