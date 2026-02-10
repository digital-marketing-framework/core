<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\FatalMigrationException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\MigrationContext;
use DigitalMarketingFramework\Core\DataSource\DataSourceManagerInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Model\ConfigurationDocument\DataSourceMigratable;
use DigitalMarketingFramework\Core\Model\ConfigurationDocument\MigratableInterface;
use DigitalMarketingFramework\Core\Model\ConfigurationDocument\StorageMigratable;
use DigitalMarketingFramework\Core\Model\DataSource\DataSourceInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManagerAwareInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManagerAwareTrait;
use DigitalMarketingFramework\Core\Notification\NotificationManagerInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class ConfigurationDocumentMaintenanceService implements ConfigurationDocumentMaintenanceServiceInterface, ConfigurationDocumentManagerAwareInterface, LoggerAwareInterface, NotificationManagerAwareInterface
{
    use ConfigurationDocumentManagerAwareTrait;
    use LoggerAwareTrait;
    use NotificationManagerAwareTrait;

    /**
     * Prefixes for system-managed documents excluded from migration listings.
     * ALS: = aliases (readonly, resolve to other documents)
     * SYS: = generated documents (programmatic, always current, readonly)
     *
     * @var array<string>
     */
    protected const EXCLUDED_PREFIXES = ['ALS:', 'SYS:'];

    /** @var array<DataSourceManagerInterface<DataSourceInterface>> */
    protected array $dataSourceManagers = [];

    /**
     * @param array<DataSourceManagerInterface<DataSourceInterface>> $dataSourceManagers
     */
    public function setDataSourceManagers(array $dataSourceManagers): void
    {
        $this->dataSourceManagers = $dataSourceManagers;
    }

    protected function isExcluded(string $identifier): bool
    {
        foreach (static::EXCLUDED_PREFIXES as $prefix) {
            if (str_starts_with($identifier, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function getAllMigratables(SchemaDocument $schemaDocument): array
    {
        $manager = $this->configurationDocumentManager;
        $migrationService = $manager->getMigrationService();

        /** @var array<string, MigratableInterface> $migratables */
        $migratables = [];

        // Phase 1a: collect storage-backed documents (excluding aliases)
        foreach ($manager->getDocumentIdentifiers() as $identifier) {
            if ($this->isExcluded($identifier)) {
                continue;
            }

            $info = $manager->getDocumentInformation($identifier);

            $migratable = new StorageMigratable(
                $identifier,
                $info->getName(),
                $info->getReadonly(),
                $manager
            );
            $migratable->setIncludes($info->getIncludes());

            $migratables[$identifier] = $migratable;
        }

        // Phase 1b: collect data source documents (deduplicated by identifier)
        foreach ($this->dataSourceManagers as $dataSourceManager) {
            foreach ($dataSourceManager->getAllDataSourceVariants() as $dataSource) {
                $identifier = $dataSource->getIdentifier();

                if (isset($migratables[$identifier])) {
                    continue;
                }

                $document = $dataSource->getConfigurationDocument();
                $configuration = $document !== '' ? $manager->getDocumentConfigurationFromDocument($document) : [];
                $includes = $manager->getIncludes($configuration);

                $migratable = new DataSourceMigratable($dataSource, $dataSourceManager);
                $migratable->setIncludes($includes);

                $migratables[$identifier] = $migratable;
            }
        }

        // Phase 2: compute reverse edges (includedBy)
        foreach ($migratables as $identifier => $migratable) {
            foreach ($migratable->getIncludes() as $parentIdentifier) {
                if (isset($migratables[$parentIdentifier])) {
                    $parent = $migratables[$parentIdentifier];
                    $parent->setIncludedBy([...$parent->getIncludedBy(), $identifier]);
                }
            }
        }

        // Phase 3: compute empty and outdated status, per-package migration info
        foreach ($migratables as $migratable) {
            $document = $migratable->getConfigurationDocument();
            if ($document === '') {
                $migratable->setEmpty(true);
                continue;
            }

            $configuration = $manager->getDocumentConfigurationFromDocument($document);
            $outdated = $migrationService->outdated($configuration, $schemaDocument);
            $migratable->setOutdated($outdated);

            if ($outdated) {
                $migratable->setMigrationInfo($this->computeMigrationInfo($configuration, $schemaDocument));
            }
        }

        // Phase 4: disable individual migration for parents that have outdated children
        foreach ($migratables as $migratable) {
            if ($migratable->isOutdated()) {
                foreach ($migratable->getIncludes() as $parentIdentifier) {
                    if (isset($migratables[$parentIdentifier])) {
                        $migratables[$parentIdentifier]->setCanMigrateIndividually(false);
                    }
                }
            }
        }

        // Phase 5: compute hasOutdatedParents
        foreach ($migratables as $migratable) {
            $hasOutdatedParents = $this->computeHasOutdatedParents($migratable, $migratables);
            $migratable->setHasOutdatedParents($hasOutdatedParents);
        }

        return $migratables;
    }

    /**
     * Check if any ancestor (parent, grandparent, etc.) is outdated.
     *
     * @param array<string, MigratableInterface> $migratables
     */
    protected function computeHasOutdatedParents(MigratableInterface $migratable, array $migratables): bool
    {
        foreach ($migratable->getIncludes() as $parentIdentifier) {
            if (!isset($migratables[$parentIdentifier])) {
                continue;
            }

            $parent = $migratables[$parentIdentifier];
            if ($parent->isOutdated()) {
                return true;
            }

            if ($this->computeHasOutdatedParents($parent, $migratables)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compute children-first ordering using Kahn's algorithm on the include graph.
     *
     * In the include graph, edges go from child to parent (child includes parent).
     * Kahn's algorithm naturally produces children-first order: nodes with no
     * incoming edges (leaves — nobody includes them) are processed first.
     *
     * @param array<string, MigratableInterface> $migratables
     *
     * @return array<string> Document identifiers in children-first order
     */
    protected function computeChildrenFirstOrder(array $migratables): array
    {
        // Compute in-degree: how many documents include this one
        $inDegree = [];
        foreach ($migratables as $identifier => $migratable) {
            if (!isset($inDegree[$identifier])) {
                $inDegree[$identifier] = 0;
            }

            foreach ($migratable->getIncludes() as $parentIdentifier) {
                if (isset($migratables[$parentIdentifier])) {
                    if (!isset($inDegree[$parentIdentifier])) {
                        $inDegree[$parentIdentifier] = 0;
                    }

                    ++$inDegree[$parentIdentifier];
                }
            }
        }

        // Start with leaves (in-degree 0 = nobody includes them)
        $queue = [];
        foreach ($inDegree as $identifier => $degree) {
            if ($degree === 0) {
                $queue[] = $identifier;
            }
        }

        $order = [];
        while ($queue !== []) {
            $identifier = array_shift($queue);
            $order[] = $identifier;

            foreach ($migratables[$identifier]->getIncludes() as $parentIdentifier) {
                if (isset($inDegree[$parentIdentifier])) {
                    --$inDegree[$parentIdentifier];
                    if ($inDegree[$parentIdentifier] === 0) {
                        $queue[] = $parentIdentifier;
                    }
                }
            }
        }

        return $order;
    }

    /**
     * Build a deduplicated, ordered list of all migratable identifiers.
     *
     * Storage-backed documents come first, then data source variants.
     * First occurrence wins (storage takes precedence, first manager wins for duplicates).
     *
     * @return array<string, string> identifier => source ('storage' or 'dataSource')
     */
    protected function buildIdentifierIndex(): array
    {
        $index = [];

        foreach ($this->configurationDocumentManager->getDocumentIdentifiers() as $identifier) {
            if ($this->isExcluded($identifier)) {
                continue;
            }

            $index[$identifier] = 'storage';
        }

        foreach ($this->dataSourceManagers as $dataSourceManager) {
            foreach ($dataSourceManager->getAllDataSourceVariantIdentifiers() as $identifier) {
                if (!isset($index[$identifier])) {
                    $index[$identifier] = 'dataSource';
                }
            }
        }

        return $index;
    }

    public function getMigratableCount(): int
    {
        return count($this->buildIdentifierIndex());
    }

    public function getMigratablePage(SchemaDocument $schemaDocument, int $offset, int $limit): array
    {
        $index = $this->buildIdentifierIndex();
        $page = array_slice($index, $offset, $limit, true);

        /** @var array<string, MigratableInterface> $migratables */
        $migratables = [];

        foreach ($page as $identifier => $source) {
            $migratable = $this->buildMigratable($identifier, $source);
            if ($migratable instanceof MigratableInterface) {
                $migratables[$identifier] = $migratable;
            }
        }

        $this->computeMigratableStatus($migratables, $schemaDocument);

        return $migratables;
    }

    public function getMigratableByIdentifier(string $identifier, SchemaDocument $schemaDocument): ?MigratableInterface
    {
        if ($this->isExcluded($identifier)) {
            return null;
        }

        $migratable = $this->buildMigratable($identifier, null);
        if (!$migratable instanceof MigratableInterface) {
            return null;
        }

        $migratables = [$identifier => $migratable];
        $this->computeMigratableStatus($migratables, $schemaDocument);

        return $migratable;
    }

    /**
     * Build a single Migratable object from an identifier.
     *
     * @param ?string $sourceHint 'storage', 'dataSource', or null to auto-detect
     */
    protected function buildMigratable(string $identifier, ?string $sourceHint): ?MigratableInterface
    {
        $manager = $this->configurationDocumentManager;

        // Try storage first
        if ($sourceHint === null || $sourceHint === 'storage') {
            if (in_array($identifier, $manager->getDocumentIdentifiers(), true)) {
                $info = $manager->getDocumentInformation($identifier);

                $migratable = new StorageMigratable(
                    $identifier,
                    $info->getName(),
                    $info->getReadonly(),
                    $manager
                );
                $migratable->setIncludes($info->getIncludes());

                return $migratable;
            }

            if ($sourceHint === 'storage') {
                return null;
            }
        }

        // Try data source managers
        foreach ($this->dataSourceManagers as $dataSourceManager) {
            $dataSource = $dataSourceManager->getDataSourceVariantByIdentifier($identifier);
            if ($dataSource === null) {
                continue;
            }

            $document = $dataSource->getConfigurationDocument();
            $configuration = $document !== '' ? $manager->getDocumentConfigurationFromDocument($document) : [];
            $includes = $manager->getIncludes($configuration);

            $migratable = new DataSourceMigratable($dataSource, $dataSourceManager);
            $migratable->setIncludes($includes);

            return $migratable;
        }

        return null;
    }

    /**
     * Compute status fields (includedBy, outdated, hasOutdatedParents) for a set of migratables.
     *
     * For paginated views, parents outside the page are resolved on-demand
     * and cached within this call to avoid redundant lookups.
     *
     * @param array<string, MigratableInterface> $migratables
     */
    protected function computeMigratableStatus(array &$migratables, SchemaDocument $schemaDocument): void
    {
        $manager = $this->configurationDocumentManager;
        $migrationService = $manager->getMigrationService();

        // Compute reverse edges within this set
        foreach ($migratables as $identifier => $migratable) {
            foreach ($migratable->getIncludes() as $parentIdentifier) {
                if (isset($migratables[$parentIdentifier])) {
                    $parent = $migratables[$parentIdentifier];
                    $parent->setIncludedBy([...$parent->getIncludedBy(), $identifier]);
                }
            }
        }

        // Compute empty and outdated status, per-package migration info
        foreach ($migratables as $migratable) {
            $document = $migratable->getConfigurationDocument();
            if ($document === '') {
                $migratable->setEmpty(true);
                continue;
            }

            $configuration = $manager->getDocumentConfigurationFromDocument($document);
            $outdated = $migrationService->outdated($configuration, $schemaDocument);
            $migratable->setOutdated($outdated);

            if ($outdated) {
                $migratable->setMigrationInfo($this->computeMigrationInfo($configuration, $schemaDocument));
            }
        }

        // Compute hasOutdatedParents (may resolve parents outside the page)
        /** @var array<string, bool> $outdatedCache */
        $outdatedCache = [];
        foreach ($migratables as $identifier => $migratable) {
            $outdatedCache[$identifier] = $migratable->isOutdated();
        }

        /** @var array<string, bool> $ancestorCache */
        $ancestorCache = [];
        foreach ($migratables as $migratable) {
            $hasOutdatedParents = $this->computeHasOutdatedParentsWithCache(
                $migratable,
                $migratables,
                $outdatedCache,
                $ancestorCache,
                $schemaDocument
            );
            $migratable->setHasOutdatedParents($hasOutdatedParents);
        }
    }

    /**
     * Check if any ancestor is outdated, with caching and on-demand resolution
     * for parents outside the current migratable set.
     *
     * Uses a separate hasOutdatedAncestors cache to avoid redundant recursive traversals.
     *
     * @param array<string, MigratableInterface> $migratables
     * @param array<string, bool> $outdatedCache identifier => isOutdated
     * @param array<string, bool> $ancestorCache identifier => hasOutdatedAncestors (populated during traversal)
     */
    protected function computeHasOutdatedParentsWithCache(
        MigratableInterface $migratable,
        array $migratables,
        array &$outdatedCache,
        array &$ancestorCache,
        SchemaDocument $schemaDocument,
    ): bool {
        $manager = $this->configurationDocumentManager;
        $migrationService = $manager->getMigrationService();

        foreach ($migratable->getIncludes() as $parentIdentifier) {
            // Already computed full ancestor result for this parent?
            if (isset($ancestorCache[$parentIdentifier])) {
                if ($outdatedCache[$parentIdentifier] || $ancestorCache[$parentIdentifier]) {
                    return true;
                }

                continue;
            }

            // Resolve parent's outdated status if not cached
            if (!isset($outdatedCache[$parentIdentifier])) {
                $parentMigratable = $this->buildMigratable($parentIdentifier, null);
                if (!$parentMigratable instanceof MigratableInterface) {
                    $outdatedCache[$parentIdentifier] = false;
                    $ancestorCache[$parentIdentifier] = false;
                    continue;
                }

                $document = $parentMigratable->getConfigurationDocument();
                $configuration = $manager->getDocumentConfigurationFromDocument($document);
                $outdatedCache[$parentIdentifier] = $migrationService->outdated($configuration, $schemaDocument);
            }

            if ($outdatedCache[$parentIdentifier]) {
                return true;
            }

            // Recurse into parent's ancestors
            $parentMigratableObj = $migratables[$parentIdentifier] ?? $this->buildMigratable($parentIdentifier, null);
            if ($parentMigratableObj !== null) {
                $parentHasOutdated = $this->computeHasOutdatedParentsWithCache(
                    $parentMigratableObj,
                    $migratables,
                    $outdatedCache,
                    $ancestorCache,
                    $schemaDocument
                );
                $ancestorCache[$parentIdentifier] = $parentHasOutdated;

                if ($parentHasOutdated) {
                    return true;
                }
            } else {
                $ancestorCache[$parentIdentifier] = false;
            }
        }

        return false;
    }

    /**
     * Compute per-package migration info by running the migration in memory.
     *
     * The migration service records per-key results (from/to versions, genuine change flags)
     * on the MigrationContext as it processes each package's migration chain.
     *
     * @param array<string, mixed> $configuration
     *
     * @return array<string, array{from: string, to: string, status: string, message: string}>
     */
    protected function computeMigrationInfo(array $configuration, SchemaDocument $schemaDocument): array
    {
        $manager = $this->configurationDocumentManager;
        $migrationService = $manager->getMigrationService();

        $stack = $manager->getConfigurationStackFromConfiguration($configuration, $schemaDocument, false);
        if (count($stack) < 2) {
            return [];
        }

        $sysDefaults = $stack[0];
        $parentStack = array_slice($stack, 1, -1);
        $context = new MigrationContext($parentStack, $sysDefaults);

        try {
            $migrationService->migrateConfiguration($configuration, $context, $schemaDocument);
        } catch (FatalMigrationException) {
            // The error is already recorded on the context by migrateConfigurationByKey().
            // Keys processed before the failure have their real status.
            // Keys after the failure are simply absent — the error is the priority.
        }

        return $context->getMigrationInfo();
    }

    public function migrateAll(SchemaDocument $schemaDocument): array
    {
        $migratables = $this->getAllMigratables($schemaDocument);
        $order = $this->computeChildrenFirstOrder($migratables);

        $result = [
            'migrated' => [],
            'skipped' => [],
            'failed' => [],
        ];

        foreach ($order as $identifier) {
            $migratable = $migratables[$identifier] ?? null;
            if ($migratable === null) {
                continue;
            }

            if (!$migratable->isOutdated()) {
                continue;
            }

            if ($migratable->isReadOnly()) {
                $result['skipped'][] = $identifier;
                continue;
            }

            try {
                $migrated = $this->migrateDocumentInternal($migratable, $schemaDocument);
                if ($migrated) {
                    $result['migrated'][] = $identifier;
                }
            } catch (FatalMigrationException $exception) {
                $this->logger->error(sprintf(
                    'Fatal migration error for document "%s": %s',
                    $identifier,
                    $exception->getMessage()
                ));
                $result['failed'][$identifier] = $exception->getMessage();

                $this->notificationManager->notify(
                    sprintf('Configuration migration failed for "%s"', $identifier),
                    $exception->getMessage(),
                    component: 'migration',
                    level: NotificationManagerInterface::LEVEL_ERROR,
                );
            }
        }

        return $result;
    }

    public function migrateDocument(MigratableInterface $migratable, SchemaDocument $schemaDocument): bool
    {
        return $this->migrateDocumentInternal($migratable, $schemaDocument);
    }

    /**
     * Migrate a single document: build its full stack, migrate in-memory via
     * migrateStackInMemory (Option C iterative), then save the leaf result.
     */
    protected function migrateDocumentInternal(MigratableInterface $migratable, SchemaDocument $schemaDocument): bool
    {
        $manager = $this->configurationDocumentManager;
        $migrationService = $manager->getMigrationService();
        $targetVersion = $schemaDocument->getVersion();

        $document = $migratable->getConfigurationDocument();
        $configuration = $manager->getDocumentConfigurationFromDocument($document);

        if (!$migrationService->outdated($configuration, $schemaDocument)) {
            return false;
        }

        // Build the full configuration stack: [SYS:defaults, ...parents, thisDocument]
        // Explicitly disable in-memory migration — we'll run migrateStackInMemory ourselves
        $stack = $manager->getConfigurationStackFromConfiguration($configuration, $schemaDocument, false);

        if (count($stack) < 2) {
            return false;
        }

        // Migrate the entire stack in memory (Option C iterative handles parent versioning)
        $migrationService->migrateStackInMemory($stack, $targetVersion);

        // The last element is the migrated document
        $migratedConfiguration = $stack[count($stack) - 1];

        // Produce the document string and save via the Migratable's mechanism
        $migratedDocument = $manager->getParser()->produceDocument($migratedConfiguration, $schemaDocument);
        $migratable->saveConfigurationDocument($migratedDocument, $schemaDocument);

        return true;
    }

    public function markAsResolved(MigratableInterface $migratable, SchemaDocument $schemaDocument): void
    {
        $manager = $this->configurationDocumentManager;
        $targetVersions = $schemaDocument->getVersion();

        $document = $migratable->getConfigurationDocument();
        $configuration = $manager->getDocumentConfigurationFromDocument($document);

        // Stamp all version tags to current schema versions
        foreach ($targetVersions as $key => $version) {
            if ($version === SchemaDocument::INITIAL_VERSION) {
                // Remove implicit baseline versions — they are not written to documents
                unset($configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION][$key]);
            } else {
                $configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION][$key] = $version;
            }
        }

        // Clean up empty version/metaData arrays
        if (isset($configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION])
            && $configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION] === []
        ) {
            unset($configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]);
        }

        if (isset($configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA])
            && $configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA] === []
        ) {
            unset($configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA]);
        }

        $resolvedDocument = $manager->getParser()->produceDocument($configuration, $schemaDocument);
        $migratable->saveConfigurationDocument($resolvedDocument, $schemaDocument);
    }

    public function resetVariant(MigratableInterface $variant, SchemaDocument $schemaDocument): bool
    {
        $baseIdentifier = $variant->getBaseMigratableIdentifier();
        if ($baseIdentifier === null) {
            return false;
        }

        $baseMigratable = $this->buildMigratable($baseIdentifier, null);
        if (!$baseMigratable instanceof MigratableInterface) {
            return false;
        }

        $baseDocument = $baseMigratable->getConfigurationDocument();
        $variant->saveConfigurationDocument($baseDocument, $schemaDocument);

        return true;
    }
}
