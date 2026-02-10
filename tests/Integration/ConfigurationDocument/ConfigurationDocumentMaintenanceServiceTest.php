<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentMaintenanceService;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationService;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\FatalMigrationException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\MigrationContext;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\Log\LoggerInterface;
use DigitalMarketingFramework\Core\Model\ConfigurationDocument\ConfigurationDocumentInformation;
use DigitalMarketingFramework\Core\Notification\NotificationManagerInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigurationDocumentMaintenanceService::class)]
class ConfigurationDocumentMaintenanceServiceTest extends TestCase
{
    protected ConfigurationDocumentMaintenanceService $subject;

    protected ConfigurationDocumentMigrationService $migrationService;

    protected ConfigurationDocumentManagerInterface&MockObject $manager;

    protected ConfigurationDocumentParserInterface&MockObject $parser;

    protected LoggerInterface&MockObject $logger;

    protected LoggerInterface&MockObject $migrationServiceLogger;

    protected NotificationManagerInterface&MockObject $notificationManager;

    /**
     * Registered test documents.
     *
     * @var array<string, array{
     *     name: string,
     *     readOnly: bool,
     *     includes: array<string>,
     *     configuration: array<string, mixed>,
     *     document: string,
     * }>
     */
    protected array $documents = [];

    /** @var array<array{id: string, delta: array<string, mixed>}> */
    protected array $migrationCallLog = [];

    /** @var array<array{identifier: string, document: string}> */
    protected array $saveLog = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->migrationServiceLogger = $this->createMock(LoggerInterface::class);
        $this->parser = $this->createMock(ConfigurationDocumentParserInterface::class);

        // Real migration service
        $this->migrationService = new ConfigurationDocumentMigrationService();
        $this->migrationService->setLogger($this->migrationServiceLogger);

        // Mock manager
        $this->manager = $this->createMock(ConfigurationDocumentManagerInterface::class);
        $this->manager->method('getMigrationService')->willReturn($this->migrationService);
        $this->manager->method('getParser')->willReturn($this->parser);

        // Manager delegates: getDocumentIdentifiers, getDocumentInformation, getDocumentFromIdentifier
        $this->manager->method('getDocumentIdentifiers')->willReturnCallback(
            fn (): array => array_keys($this->documents)
        );

        $this->manager->method('getDocumentInformation')->willReturnCallback(
            function (string $identifier): ConfigurationDocumentInformation {
                $doc = $this->documents[$identifier];

                return new ConfigurationDocumentInformation(
                    $identifier,
                    $identifier,
                    $doc['name'],
                    $doc['readOnly'],
                    $doc['includes'],
                );
            }
        );

        $this->manager->method('getDocumentFromIdentifier')->willReturnCallback(
            fn (string $identifier): string => $this->documents[$identifier]['document'] ?? ''
        );

        $this->manager->method('getDocumentConfigurationFromDocument')->willReturnCallback(
            function (string $document): array {
                foreach ($this->documents as $doc) {
                    if ($doc['document'] === $document) {
                        return $doc['configuration'];
                    }
                }

                return [];
            }
        );

        $this->manager->method('getIncludes')->willReturnCallback(
            fn (array $configuration): array => $configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_INCLUDES] ?? []
        );

        // getConfigurationStackFromConfiguration: build a simple stack
        // [SYS:defaults, ...parents, thisDocument]
        $this->manager->method('getConfigurationStackFromConfiguration')->willReturnCallback(
            function (array $configuration): array {
                $sysDefaults = $this->buildConfig(
                    $this->getSchemaTargetVersions(),
                    ['sys' => 'defaults']
                );

                $parentStack = $this->buildParentStack($configuration);

                return [$sysDefaults, ...$parentStack, $configuration];
            }
        );

        // saveDocument: record calls
        $this->manager->method('saveDocument')->willReturnCallback(
            function (string $identifier, string $document): void {
                $this->saveLog[] = ['identifier' => $identifier, 'document' => $document];
            }
        );

        // Parser: produceDocument returns a deterministic string
        $this->parser->method('produceDocument')->willReturnCallback(
            fn (array $configuration): string => 'document:' . json_encode($configuration)
        );

        // Notification manager
        $this->notificationManager = $this->createMock(NotificationManagerInterface::class);

        // Create subject
        $this->subject = new ConfigurationDocumentMaintenanceService();
        $this->subject->setConfigurationDocumentManager($this->manager);
        $this->subject->setLogger($this->logger);
        $this->subject->setNotificationManager($this->notificationManager);
        $this->subject->setDataSourceManagers([]);

        $this->migrationCallLog = [];
        $this->saveLog = [];
    }

    // -- Helpers --

    /** @var array<string, string> */
    protected array $schemaTargetVersions = [];

    /**
     * @return array<string, string>
     */
    protected function getSchemaTargetVersions(): array
    {
        return $this->schemaTargetVersions;
    }

    /**
     * Build parent stack for a configuration by resolving its includes recursively.
     *
     * @param array<string, mixed> $configuration
     *
     * @return array<array<string, mixed>>
     */
    protected function buildParentStack(array $configuration): array
    {
        $includes = $configuration[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_INCLUDES] ?? [];
        $stack = [];

        foreach ($includes as $parentIdentifier) {
            if (isset($this->documents[$parentIdentifier])) {
                $parentConfig = $this->documents[$parentIdentifier]['configuration'];
                $grandParentStack = $this->buildParentStack($parentConfig);
                $stack = [...$stack, ...$grandParentStack, $parentConfig];
            }
        }

        return $stack;
    }

    /**
     * @param array<string, string> $versionTags
     * @param array<string, mixed> $data
     * @param array<string> $includes
     *
     * @return array<string, mixed>
     */
    protected function buildConfig(array $versionTags = [], array $data = [], array $includes = []): array
    {
        $config = $data;
        if ($versionTags !== []) {
            $config[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION] = $versionTags;
        }

        if ($includes !== []) {
            $config[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_INCLUDES] = $includes;
        }

        return $config;
    }

    /**
     * @param array<string, string> $versions
     */
    protected function createSchemaDocument(array $versions): SchemaDocument
    {
        $this->schemaTargetVersions = $versions;
        $schema = new SchemaDocument();
        foreach ($versions as $key => $version) {
            $schema->addVersion($key, $version);
        }

        return $schema;
    }

    /**
     * Register a test document in the mock manager.
     *
     * @param array<string, string> $versionTags
     * @param array<string, mixed> $data
     * @param array<string> $includes
     */
    protected function registerDocument(
        string $identifier,
        string $name,
        bool $readOnly,
        array $versionTags = [],
        array $data = [],
        array $includes = [],
    ): void {
        $configuration = $this->buildConfig($versionTags, $data, $includes);

        $this->documents[$identifier] = [
            'name' => $name,
            'readOnly' => $readOnly,
            'includes' => $includes,
            'configuration' => $configuration,
            'document' => 'doc:' . $identifier,
        ];
    }

    /**
     * @param (callable(array<string, mixed>, MigrationContext): array<string, mixed>)|null $transform
     */
    protected function createTrackedMigration(
        string $key,
        string $sourceVersion,
        string $targetVersion,
        ?callable $transform = null,
    ): ConfigurationDocumentMigrationInterface&MockObject {
        $mock = $this->createMock(ConfigurationDocumentMigrationInterface::class);
        $mock->method('getKey')->willReturn($key);
        $mock->method('getSourceVersion')->willReturn($sourceVersion);
        $mock->method('getTargetVersion')->willReturn($targetVersion);
        $mock->method('checkVersions')->willReturn(true);

        $id = $key . ':' . $sourceVersion . '→' . $targetVersion;
        $callLog = &$this->migrationCallLog;

        $mock->method('migrate')->willReturnCallback(
            function (array $delta, MigrationContext $context) use ($id, &$callLog, $transform): array {
                $callLog[] = [
                    'id' => $id,
                    'delta' => $delta,
                ];

                return $transform !== null ? $transform($delta, $context) : $delta;
            }
        );

        return $mock;
    }

    /**
     * @return array<string>
     */
    protected function getMigrationCallIds(): array
    {
        return array_column($this->migrationCallLog, 'id');
    }

    /**
     * @return array<string>
     */
    protected function getSaveOrder(): array
    {
        return array_column($this->saveLog, 'identifier');
    }

    // =========================================================================
    // Group 1: getAllMigratables() — Discovery & Status
    // =========================================================================

    #[Test]
    public function getAllMigratablesDiscoversStorageDocuments(): void
    {
        $this->registerDocument('doc-a', 'Document A', false, ['pkg-a' => '1.0.1']);
        $this->registerDocument('doc-b', 'Document B', true, ['pkg-a' => '1.0.1']);

        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);
        $migratables = $this->subject->getAllMigratables($schema);

        self::assertCount(2, $migratables);
        self::assertArrayHasKey('doc-a', $migratables);
        self::assertArrayHasKey('doc-b', $migratables);
        self::assertSame('Document A', $migratables['doc-a']->getName());
        self::assertSame('Document B', $migratables['doc-b']->getName());
        self::assertFalse($migratables['doc-a']->isReadOnly());
        self::assertTrue($migratables['doc-b']->isReadOnly());
    }

    #[Test]
    public function getAllMigratablesComputesReverseEdges(): void
    {
        $this->registerDocument('parent', 'Parent', false, ['pkg-a' => '1.0.1']);
        $this->registerDocument('child', 'Child', false, ['pkg-a' => '1.0.1'], [], ['parent']);

        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);
        $migratables = $this->subject->getAllMigratables($schema);

        self::assertSame(['child'], $migratables['parent']->getIncludedBy());
        self::assertSame([], $migratables['child']->getIncludedBy());
    }

    #[Test]
    public function getAllMigratablesComputesOutdatedStatus(): void
    {
        $this->registerDocument('current', 'Current', false, ['pkg-a' => '1.0.1']);
        $this->registerDocument('outdated', 'Outdated', false, ['pkg-a' => '1.0.0']);

        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);
        $migratables = $this->subject->getAllMigratables($schema);

        self::assertFalse($migratables['current']->isOutdated());
        self::assertTrue($migratables['outdated']->isOutdated());
    }

    #[Test]
    public function getAllMigratablesComputesHasOutdatedParents(): void
    {
        $this->registerDocument('root', 'Root', false, ['pkg-a' => '1.0.0']);
        $this->registerDocument('child', 'Child', false, ['pkg-a' => '1.0.1'], [], ['root']);
        $this->registerDocument('grandchild', 'Grandchild', false, ['pkg-a' => '1.0.1'], [], ['child']);

        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);
        $migratables = $this->subject->getAllMigratables($schema);

        // Root is outdated
        self::assertTrue($migratables['root']->isOutdated());
        self::assertFalse($migratables['root']->hasOutdatedParents());

        // Child is current but has an outdated parent
        self::assertFalse($migratables['child']->isOutdated());
        self::assertTrue($migratables['child']->hasOutdatedParents());

        // Grandchild is current but has an outdated ancestor
        self::assertFalse($migratables['grandchild']->isOutdated());
        self::assertTrue($migratables['grandchild']->hasOutdatedParents());
    }

    // =========================================================================
    // Group 2: migrateAll() — Ordering & Save Decisions
    // =========================================================================

    #[Test]
    public function migrateAllLinearChainChildrenFirst(): void
    {
        $this->migrationService->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));

        // A → B → C (C includes B includes A)
        $this->registerDocument('doc-a', 'A', false, [], ['a' => 'val']);
        $this->registerDocument('doc-b', 'B', false, [], ['b' => 'val'], ['doc-a']);
        $this->registerDocument('doc-c', 'C', false, [], ['c' => 'val'], ['doc-b']);

        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);
        $result = $this->subject->migrateAll($schema);

        // All three migrated
        self::assertCount(3, $result['migrated']);
        self::assertContains('doc-a', $result['migrated']);
        self::assertContains('doc-b', $result['migrated']);
        self::assertContains('doc-c', $result['migrated']);

        // Save order: children first (C before B before A)
        $saveOrder = $this->getSaveOrder();
        $posC = array_search('doc-c', $saveOrder, true);
        $posB = array_search('doc-b', $saveOrder, true);
        $posA = array_search('doc-a', $saveOrder, true);
        self::assertLessThan($posB, $posC, 'C should be saved before B');
        self::assertLessThan($posA, $posB, 'B should be saved before A');
    }

    #[Test]
    public function migrateAllDiamondInheritance(): void
    {
        $this->migrationService->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));

        // A and B both include C (diamond: C is shared parent)
        $this->registerDocument('doc-c', 'C', false, [], ['c' => 'val']);
        $this->registerDocument('doc-a', 'A', false, [], ['a' => 'val'], ['doc-c']);
        $this->registerDocument('doc-b', 'B', false, [], ['b' => 'val'], ['doc-c']);

        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);
        $result = $this->subject->migrateAll($schema);

        self::assertCount(3, $result['migrated']);

        // C must be saved last (both A and B depend on it)
        $saveOrder = $this->getSaveOrder();
        $posC = array_search('doc-c', $saveOrder, true);
        $posA = array_search('doc-a', $saveOrder, true);
        $posB = array_search('doc-b', $saveOrder, true);
        self::assertGreaterThan($posA, $posC, 'C should be saved after A');
        self::assertGreaterThan($posB, $posC, 'C should be saved after B');
    }

    #[Test]
    public function migrateAllCurrentDocumentsNotSaved(): void
    {
        $this->migrationService->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));

        $this->registerDocument('current', 'Current', false, ['pkg-a' => '1.0.1'], ['val' => 'x']);
        $this->registerDocument('outdated', 'Outdated', false, [], ['val' => 'y']);

        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);
        $result = $this->subject->migrateAll($schema);

        self::assertSame(['outdated'], $result['migrated']);
        self::assertCount(1, $this->saveLog);
        self::assertSame('outdated', $this->saveLog[0]['identifier']);
    }

    #[Test]
    public function migrateAllReadonlyDocumentsSkipped(): void
    {
        $this->migrationService->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));

        $this->registerDocument('readonly-doc', 'Readonly', true, [], ['val' => 'x']);
        $this->registerDocument('writable-doc', 'Writable', false, [], ['val' => 'y']);

        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);
        $result = $this->subject->migrateAll($schema);

        self::assertSame(['readonly-doc'], $result['skipped']);
        self::assertSame(['writable-doc'], $result['migrated']);

        // Only writable saved
        self::assertCount(1, $this->saveLog);
        self::assertSame('writable-doc', $this->saveLog[0]['identifier']);
    }

    #[Test]
    public function migrateAllFatalErrorIsolated(): void
    {
        // Register a migration that throws for a specific document
        $throwingMigration = $this->createMock(ConfigurationDocumentMigrationInterface::class);
        $throwingMigration->method('getKey')->willReturn('pkg-a');
        $throwingMigration->method('getSourceVersion')->willReturn('1.0.0');
        $throwingMigration->method('getTargetVersion')->willReturn('1.0.1');
        $throwingMigration->method('checkVersions')->willReturn(true);
        $throwingMigration->method('migrate')->willReturnCallback(
            function (array $delta): array {
                if (isset($delta['fail'])) {
                    throw new FatalMigrationException('test fatal error');
                }

                return $delta;
            }
        );
        $this->migrationService->addMigration($throwingMigration);

        // doc-fail will throw, doc-ok should still be migrated
        // No includes so they are independent — order doesn't matter
        $this->registerDocument('doc-fail', 'Fail', false, [], ['fail' => true]);
        $this->registerDocument('doc-ok', 'OK', false, [], ['ok' => true]);

        $this->notificationManager->expects(self::once())
            ->method('notify')
            ->with(
                self::stringContains('doc-fail'),
                'test fatal error',
                '',
                null,
                'migration',
                NotificationManagerInterface::LEVEL_ERROR,
            );

        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);
        $result = $this->subject->migrateAll($schema);

        self::assertContains('doc-ok', $result['migrated']);
        self::assertArrayHasKey('doc-fail', $result['failed']);
        self::assertSame('test fatal error', $result['failed']['doc-fail']);
    }

    #[Test]
    public function migrateAllResultArraysCorrect(): void
    {
        $this->migrationService->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));

        $this->registerDocument('current', 'Current', false, ['pkg-a' => '1.0.1']);
        $this->registerDocument('outdated', 'Outdated', false, [], ['val' => 'x']);
        $this->registerDocument('readonly', 'Readonly', true, []);

        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);
        $result = $this->subject->migrateAll($schema);

        self::assertSame(['outdated'], $result['migrated']);
        self::assertSame(['readonly'], $result['skipped']);
        self::assertSame([], $result['failed']);
    }

    // =========================================================================
    // Group 3: migrateDocument() — Single Document
    // =========================================================================

    #[Test]
    public function migrateDocumentMigratesAndSaves(): void
    {
        $this->migrationService->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));

        $this->registerDocument('doc', 'Doc', false, [], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        $migratables = $this->subject->getAllMigratables($schema);
        $result = $this->subject->migrateDocument($migratables['doc'], $schema);

        self::assertTrue($result);
        self::assertCount(1, $this->saveLog);
        self::assertSame('doc', $this->saveLog[0]['identifier']);
    }

    #[Test]
    public function migrateDocumentCurrentDocumentReturnsFalse(): void
    {
        $this->registerDocument('doc', 'Doc', false, ['pkg-a' => '1.0.1'], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        $migratables = $this->subject->getAllMigratables($schema);
        $result = $this->subject->migrateDocument($migratables['doc'], $schema);

        self::assertFalse($result);
        self::assertCount(0, $this->saveLog);
    }

    #[Test]
    public function migrateDocumentSavesCorrectMigratedContent(): void
    {
        // Migration that adds a marker to the delta
        $this->migrationService->addMigration($this->createTrackedMigration(
            'pkg-a',
            '1.0.0',
            '1.0.1',
            function (array $delta): array {
                $delta['migrated'] = true;

                return $delta;
            }
        ));

        $this->registerDocument('doc', 'Doc', false, [], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        $migratables = $this->subject->getAllMigratables($schema);
        $this->subject->migrateDocument($migratables['doc'], $schema);

        // The parser's produceDocument was called with the migrated configuration
        // and its output was passed to saveDocument
        self::assertCount(1, $this->saveLog);
        $savedDocument = $this->saveLog[0]['document'];

        // The saved document should be the parser's output for the migrated config
        // Parser returns 'document:' + json, so we can verify the migrated data is in there
        self::assertStringContainsString('"migrated":true', $savedDocument);
        self::assertStringContainsString('"field":"value"', $savedDocument);
    }
}
