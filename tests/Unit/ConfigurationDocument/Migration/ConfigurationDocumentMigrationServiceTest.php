<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\ConfigurationDocument\Migration;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationService;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\FatalMigrationException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\MigrationContext;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\MigrationException;
use DigitalMarketingFramework\Core\Log\LoggerInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigurationDocumentMigrationService::class)]
class ConfigurationDocumentMigrationServiceTest extends TestCase
{
    protected ConfigurationDocumentMigrationService $subject;

    protected LoggerInterface&MockObject $logger;

    /** @var array<array{id: string, delta: array<string, mixed>}> */
    protected array $callLog = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->subject = new ConfigurationDocumentMigrationService();
        $this->subject->setLogger($this->logger);

        $this->callLog = [];
    }

    // -- Helpers --

    /**
     * @param array<string, string> $versionTags
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function buildConfig(array $versionTags = [], array $data = []): array
    {
        $config = $data;
        if ($versionTags !== []) {
            $config[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION] = $versionTags;
        }

        return $config;
    }

    /**
     * @param array<string, string> $versions
     */
    protected function createSchemaDocument(array $versions): SchemaDocument
    {
        $schema = new SchemaDocument();
        foreach ($versions as $key => $version) {
            $schema->addVersion($key, $version);
        }

        return $schema;
    }

    protected function createEmptyContext(): MigrationContext
    {
        return new MigrationContext([], []);
    }

    /**
     * @param array<array<string, mixed>> $parentStack
     * @param array<string, mixed> $sysDefaults
     */
    protected function createContext(array $parentStack = [], array $sysDefaults = []): MigrationContext
    {
        return new MigrationContext($parentStack, $sysDefaults);
    }

    /**
     * Create a tracked migration mock that records calls in $this->callLog.
     *
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
        $callLog = &$this->callLog;

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
    protected function getCallIds(): array
    {
        return array_column($this->callLog, 'id');
    }

    // =========================================================================
    // Group 1: outdated()
    // =========================================================================

    #[Test]
    public function outdatedReturnsFalseWhenVersionsMatch(): void
    {
        $config = $this->buildConfig(['pkg-a' => '1.0.1']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        self::assertFalse($this->subject->outdated($config, $schema));
    }

    #[Test]
    public function outdatedReturnsTrueWhenVersionBehind(): void
    {
        $config = $this->buildConfig(['pkg-a' => '1.0.0']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        self::assertTrue($this->subject->outdated($config, $schema));
    }

    #[Test]
    public function outdatedReturnsTrueWhenVersionTagMissing(): void
    {
        $config = $this->buildConfig();
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        self::assertTrue($this->subject->outdated($config, $schema));
    }

    #[Test]
    public function outdatedReturnsFalseWithOrphanVersionTag(): void
    {
        $config = $this->buildConfig(['pkg-a' => '1.0.1', 'pkg-removed' => '2.0.0']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        self::assertFalse($this->subject->outdated($config, $schema));
    }

    #[Test]
    public function outdatedReturnsTrueWhenOneOfMultipleKeysBehind(): void
    {
        $config = $this->buildConfig(['pkg-a' => '1.0.1', 'pkg-b' => '1.0.0']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1', 'pkg-b' => '1.0.1']);

        self::assertTrue($this->subject->outdated($config, $schema));
    }

    // =========================================================================
    // Group 2: migrateConfiguration() — Chain Execution & Call Verification
    // =========================================================================

    #[Test]
    public function migrateConfigurationSingleStep(): void
    {
        $migration = $this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1');
        $this->subject->addMigration($migration);

        $config = $this->buildConfig([], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        $result = $this->subject->migrateConfiguration($config, $this->createEmptyContext(), $schema);

        self::assertCount(1, $this->callLog);
        self::assertSame('pkg-a:1.0.0→1.0.1', $this->callLog[0]['id']);
        self::assertSame('value', $this->callLog[0]['delta']['field']);
        self::assertSame('1.0.1', $result[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
    }

    #[Test]
    public function migrateConfigurationMultiStepChain(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.1', '1.0.2'));

        $config = $this->buildConfig([], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.2']);

        $result = $this->subject->migrateConfiguration($config, $this->createEmptyContext(), $schema);

        self::assertSame(['pkg-a:1.0.0→1.0.1', 'pkg-a:1.0.1→1.0.2'], $this->getCallIds());
        self::assertSame('1.0.2', $result[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
    }

    #[Test]
    public function migrateConfigurationBrokenChainStopsAtLastAvailable(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));
        // No migration for 1.0.1 → 1.0.2

        $config = $this->buildConfig([], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.2']);

        $result = $this->subject->migrateConfiguration($config, $this->createEmptyContext(), $schema);

        self::assertCount(1, $this->callLog);
        self::assertSame('1.0.1', $result[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
    }

    #[Test]
    public function migrateConfigurationMultipleKeysRunIndependently(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));
        $this->subject->addMigration($this->createTrackedMigration('pkg-b', '1.0.0', '1.0.1'));

        $config = $this->buildConfig([], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1', 'pkg-b' => '1.0.1']);

        $result = $this->subject->migrateConfiguration($config, $this->createEmptyContext(), $schema);

        self::assertCount(2, $this->callLog);
        self::assertContains('pkg-a:1.0.0→1.0.1', $this->getCallIds());
        self::assertContains('pkg-b:1.0.0→1.0.1', $this->getCallIds());
        self::assertSame('1.0.1', $result[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
        self::assertSame('1.0.1', $result[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-b']);
    }

    #[Test]
    public function migrateConfigurationAlreadyAtTargetNoMigrationCalled(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));

        $config = $this->buildConfig(['pkg-a' => '1.0.1'], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        $this->subject->migrateConfiguration($config, $this->createEmptyContext(), $schema);

        self::assertCount(0, $this->callLog);
    }

    #[Test]
    public function migrateConfigurationContextPassedCorrectly(): void
    {
        $receivedContext = null;
        $migration = $this->createTrackedMigration(
            'pkg-a',
            '1.0.0',
            '1.0.1',
            function (array $delta, MigrationContext $context) use (&$receivedContext): array {
                $receivedContext = $context;

                return $delta;
            }
        );
        $this->subject->addMigration($migration);

        $parentConfig = ['parentKey' => 'parentValue'];
        $context = $this->createContext([$parentConfig]);

        $config = $this->buildConfig([], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        $this->subject->migrateConfiguration($config, $context, $schema);

        self::assertNotNull($receivedContext);
        self::assertSame(['parentKey' => 'parentValue'], $receivedContext->getParentConfiguration());
    }

    // =========================================================================
    // Group 3: migrateConfiguration() — Error Handling
    // =========================================================================

    #[Test]
    public function migrateConfigurationThrowsFatalOnCheckVersionsFailure(): void
    {
        $mock = $this->createMock(ConfigurationDocumentMigrationInterface::class);
        $mock->method('getKey')->willReturn('pkg-a');
        $mock->method('getSourceVersion')->willReturn('1.0.0');
        $mock->method('getTargetVersion')->willReturn('1.0.1');
        $mock->method('checkVersions')->willReturn(false);
        $this->subject->addMigration($mock);

        $config = $this->buildConfig([], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        $this->expectException(FatalMigrationException::class);
        $this->subject->migrateConfiguration($config, $this->createEmptyContext(), $schema);
    }

    #[Test]
    public function migrateConfigurationMigrationExceptionStopsChainAndLogs(): void
    {
        $throwingMigration = $this->createMock(ConfigurationDocumentMigrationInterface::class);
        $throwingMigration->method('getKey')->willReturn('pkg-a');
        $throwingMigration->method('getSourceVersion')->willReturn('1.0.0');
        $throwingMigration->method('getTargetVersion')->willReturn('1.0.1');
        $throwingMigration->method('checkVersions')->willReturn(true);
        $throwingMigration->method('migrate')->willThrowException(new MigrationException('test error'));
        $this->subject->addMigration($throwingMigration);

        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.1', '1.0.2'));

        $this->logger->expects(self::once())->method('warning')->with('test error');

        $config = $this->buildConfig([], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.2']);

        $result = $this->subject->migrateConfiguration($config, $this->createEmptyContext(), $schema);

        // Chain stopped: second migration not called
        self::assertCount(0, $this->callLog);
        // Version set to where chain stopped (implicit 1.0.0 — pre-failure)
        self::assertSame('1.0.0', $result[ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
    }

    // =========================================================================
    // Group 4: genuinelyOutdated()
    // =========================================================================

    #[Test]
    public function genuinelyOutdatedReturnsTrueWhenDataChanges(): void
    {
        $migration = $this->createTrackedMigration(
            'pkg-a',
            '1.0.0',
            '1.0.1',
            function (array $delta): array {
                $delta['newField'] = 'added';

                return $delta;
            }
        );
        $this->subject->addMigration($migration);

        $config = $this->buildConfig([], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        self::assertTrue($this->subject->genuinelyOutdated($config, $this->createEmptyContext(), $schema));
    }

    #[Test]
    public function genuinelyOutdatedReturnsFalseWhenOnlyVersionTagsChange(): void
    {
        // Migration returns delta unchanged (default behavior)
        $migration = $this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1');
        $this->subject->addMigration($migration);

        $config = $this->buildConfig([], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        self::assertFalse($this->subject->genuinelyOutdated($config, $this->createEmptyContext(), $schema));
    }

    #[Test]
    public function genuinelyOutdatedReturnsFalseWhenAlreadyCurrent(): void
    {
        $config = $this->buildConfig(['pkg-a' => '1.0.1'], ['field' => 'value']);
        $schema = $this->createSchemaDocument(['pkg-a' => '1.0.1']);

        self::assertFalse($this->subject->genuinelyOutdated($config, $this->createEmptyContext(), $schema));
    }

    // =========================================================================
    // Group 5: migrateStackInMemory() — Core Algorithm Tests
    // =========================================================================

    // -- Simple cases --

    #[Test]
    public function migrateStackInMemoryStackTooSmallIsNoop(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));

        $stack = [$this->buildConfig(['pkg-a' => '1.0.1'])]; // Only SYS:defaults
        $this->subject->migrateStackInMemory($stack, ['pkg-a' => '1.0.1']);

        self::assertCount(0, $this->callLog);
    }

    #[Test]
    public function migrateStackInMemorySingleEntryAfterSysDefaults(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));

        $stack = [
            $this->buildConfig(['pkg-a' => '1.0.1']),        // SYS:defaults (current)
            $this->buildConfig([], ['field' => 'value']),     // C at implicit 1.0.0
        ];

        $this->subject->migrateStackInMemory($stack, ['pkg-a' => '1.0.1']);

        self::assertCount(1, $this->callLog);
        self::assertSame('pkg-a:1.0.0→1.0.1', $this->callLog[0]['id']);
        self::assertSame('1.0.1', $stack[1][ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
    }

    #[Test]
    public function migrateStackInMemoryLeafOnlyOutdated(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));

        $stack = [
            $this->buildConfig(['pkg-a' => '1.0.1']),                    // SYS:defaults
            $this->buildConfig(['pkg-a' => '1.0.1'], ['p' => 'parent']), // P (current)
            $this->buildConfig([], ['c' => 'child']),                    // C (outdated)
        ];

        $this->subject->migrateStackInMemory($stack, ['pkg-a' => '1.0.1']);

        self::assertCount(1, $this->callLog);
        self::assertSame('pkg-a:1.0.0→1.0.1', $this->callLog[0]['id']);
    }

    #[Test]
    public function migrateStackInMemoryAllCurrentIsNoop(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));

        $stack = [
            $this->buildConfig(['pkg-a' => '1.0.1']),                    // SYS:defaults
            $this->buildConfig(['pkg-a' => '1.0.1'], ['p' => 'parent']), // P (current)
            $this->buildConfig(['pkg-a' => '1.0.1'], ['c' => 'child']), // C (current)
        ];

        $this->subject->migrateStackInMemory($stack, ['pkg-a' => '1.0.1']);

        self::assertCount(0, $this->callLog);
    }

    // -- Option C iterative: parent versioning --

    #[Test]
    public function migrateStackInMemoryParentBehindChild(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.1', '1.0.2'));

        $stack = [
            $this->buildConfig(['pkg-a' => '1.0.2']),                    // SYS:defaults
            $this->buildConfig([], ['p' => 'parent']),                   // P at implicit 1.0.0
            $this->buildConfig(['pkg-a' => '1.0.1'], ['c' => 'child']), // C at 1.0.1
        ];

        $this->subject->migrateStackInMemory($stack, ['pkg-a' => '1.0.2']);

        $callIds = $this->getCallIds();

        // C needs step 1.0.1→1.0.2. Option C: P must be at 1.0.1 first.
        // P migrated 1.0.0→1.0.1 (in-memory, for context), then C migrated 1.0.1→1.0.2.
        // Then P is processed directly: needs 1.0.1→1.0.2.
        self::assertContains('pkg-a:1.0.0→1.0.1', $callIds);
        self::assertContains('pkg-a:1.0.1→1.0.2', $callIds);

        // P's migration (1.0.0→1.0.1) must happen before C's migration (1.0.1→1.0.2) in the log
        $pMigrationIndex = array_search('pkg-a:1.0.0→1.0.1', $callIds, true);
        $cMigrationIndex = array_search('pkg-a:1.0.1→1.0.2', $callIds, true);
        self::assertLessThan($cMigrationIndex, $pMigrationIndex);

        // Both stack entries are now at target version
        self::assertSame('1.0.2', $stack[1][ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
        self::assertSame('1.0.2', $stack[2][ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
    }

    #[Test]
    public function migrateStackInMemoryParentAlreadyAhead(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.1', '1.0.2'));

        $stack = [
            $this->buildConfig(['pkg-a' => '1.0.2']),                   // SYS:defaults
            $this->buildConfig(['pkg-a' => '1.0.2'], ['p' => 'parent']), // P already at 1.0.2
            $this->buildConfig([], ['c' => 'child']),                    // C at implicit 1.0.0
        ];

        $this->subject->migrateStackInMemory($stack, ['pkg-a' => '1.0.2']);

        $callIds = $this->getCallIds();

        // C needs 1.0.0→1.0.1→1.0.2. P is already at 1.0.2, no parent migration needed.
        self::assertSame(['pkg-a:1.0.0→1.0.1', 'pkg-a:1.0.1→1.0.2'], $callIds);

        // P unchanged at 1.0.2
        self::assertSame('1.0.2', $stack[1][ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
    }

    #[Test]
    public function migrateStackInMemoryMultiLevelCascading(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.1', '1.0.2'));
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.2', '1.0.3'));

        $stack = [
            $this->buildConfig(['pkg-a' => '1.0.3']),                    // SYS:defaults
            $this->buildConfig([], ['p1' => 'parent1']),                  // P1 at implicit 1.0.0
            $this->buildConfig([], ['p2' => 'parent2']),                  // P2 at implicit 1.0.0
            $this->buildConfig(['pkg-a' => '1.0.2'], ['c' => 'child']),  // C at 1.0.2
        ];

        $this->subject->migrateStackInMemory($stack, ['pkg-a' => '1.0.3']);

        // C needs step 1.0.2→1.0.3. Option C requires P1 and P2 to be at least 1.0.2.
        // Both P1 and P2 must be migrated in-memory before C's step.
        // Then P2 processed directly, then P1 processed directly.
        // All should reach 1.0.3.
        self::assertSame('1.0.3', $stack[1][ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
        self::assertSame('1.0.3', $stack[2][ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
        self::assertSame('1.0.3', $stack[3][ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
    }

    #[Test]
    public function migrateStackInMemoryCanonicalMixedExample(): void
    {
        // Register full migration chain 1.0.0 through 1.0.5
        for ($minor = 0; $minor < 5; ++$minor) {
            $this->subject->addMigration($this->createTrackedMigration(
                'pkg-a',
                '1.0.' . $minor,
                '1.0.' . ($minor + 1),
            ));
        }

        $stack = [
            $this->buildConfig(['pkg-a' => '1.0.5']),                    // SYS:defaults
            $this->buildConfig(['pkg-a' => '1.0.5'], ['p1' => 'v']),     // P1 at 1.0.5
            $this->buildConfig(['pkg-a' => '1.0.2'], ['p2' => 'v']),     // P2 at 1.0.2
            $this->buildConfig(['pkg-a' => '1.0.4'], ['p3' => 'v']),     // P3 at 1.0.4
            $this->buildConfig(['pkg-a' => '1.0.3'], ['c' => 'v']),      // C at 1.0.3
        ];

        $this->subject->migrateStackInMemory($stack, ['pkg-a' => '1.0.5']);

        // All stack entries should be at 1.0.5
        for ($i = 1; $i <= 4; ++$i) {
            self::assertSame(
                '1.0.5',
                $stack[$i][ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a'],
                'Stack entry ' . $i . ' should be at 1.0.5'
            );
        }

        // P2 (at 1.0.2, behind C at 1.0.3) must have been migrated.
        // P1 (at 1.0.5) and P3 (at 1.0.4, ≥ C at 1.0.3) should not have needed
        // in-memory migration for C's first step.
        $callIds = $this->getCallIds();
        self::assertContains('pkg-a:1.0.2→1.0.3', $callIds, 'P2 should be migrated from 1.0.2');
    }

    #[Test]
    public function migrateStackInMemoryParentWriteBackPersistsInStack(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.1', '1.0.2'));

        // Stack: [SYS, P@1.0.0, C1@1.0.1, C2@1.0.1]
        // C1 and C2 are both at same level, both children of P.
        // Processing order: C2 first (rightmost), then C1, then P.
        $stack = [
            $this->buildConfig(['pkg-a' => '1.0.2']),                    // SYS:defaults
            $this->buildConfig([], ['p' => 'parent']),                   // P at implicit 1.0.0
            $this->buildConfig(['pkg-a' => '1.0.1'], ['c1' => 'v']),    // C1 at 1.0.1
            $this->buildConfig(['pkg-a' => '1.0.1'], ['c2' => 'v']),    // C2 at 1.0.1
        ];

        $this->subject->migrateStackInMemory($stack, ['pkg-a' => '1.0.2']);

        // All should reach 1.0.2
        for ($i = 1; $i <= 3; ++$i) {
            self::assertSame(
                '1.0.2',
                $stack[$i][ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a'],
                'Stack entry ' . $i . ' should be at 1.0.2'
            );
        }

        // The 1.0.0→1.0.1 migration (for P) should appear in the log.
        // When C2 is processed, P must be brought up to 1.0.1.
        // When C1 is processed next, P should already be at 1.0.1 from C2's processing.
        $p10To11Count = count(array_filter($this->callLog, fn (array $entry) => $entry['id'] === 'pkg-a:1.0.0→1.0.1'));

        // P's 1.0.0→1.0.1 should happen only once (written back to stack by C2's processing)
        self::assertSame(1, $p10To11Count, 'P migration 1.0.0→1.0.1 should happen exactly once (write-back persists)');
    }

    // -- Multiple keys --

    #[Test]
    public function migrateStackInMemoryTwoIndependentKeys(): void
    {
        $this->subject->addMigration($this->createTrackedMigration('pkg-a', '1.0.0', '1.0.1'));
        $this->subject->addMigration($this->createTrackedMigration('pkg-b', '1.0.0', '1.0.1'));

        $stack = [
            $this->buildConfig(['pkg-a' => '1.0.1', 'pkg-b' => '1.0.1']), // SYS:defaults
            $this->buildConfig([], ['c' => 'child']),                       // C at implicit 1.0.0 for both
        ];

        $this->subject->migrateStackInMemory($stack, ['pkg-a' => '1.0.1', 'pkg-b' => '1.0.1']);

        self::assertContains('pkg-a:1.0.0→1.0.1', $this->getCallIds());
        self::assertContains('pkg-b:1.0.0→1.0.1', $this->getCallIds());
        self::assertSame('1.0.1', $stack[1][ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-a']);
        self::assertSame('1.0.1', $stack[1][ConfigurationDocumentManagerInterface::KEY_META_DATA][ConfigurationDocumentManagerInterface::KEY_DOCUMENT_VERSION]['pkg-b']);
    }

    // -- Children-first processing order --

    #[Test]
    public function migrateStackInMemoryProcessesLeafBeforeParent(): void
    {
        // Use transforms to distinguish which stack entry was being migrated
        $migrationLog = [];
        $migration = $this->createMock(ConfigurationDocumentMigrationInterface::class);
        $migration->method('getKey')->willReturn('pkg-a');
        $migration->method('getSourceVersion')->willReturn('1.0.0');
        $migration->method('getTargetVersion')->willReturn('1.0.1');
        $migration->method('checkVersions')->willReturn(true);
        $migration->method('migrate')->willReturnCallback(
            function (array $delta, MigrationContext $context) use (&$migrationLog): array {
                // Record which entry is being migrated based on its data
                $migrationLog[] = array_keys(array_diff_key($delta, [ConfigurationDocumentManagerInterface::KEY_META_DATA => true]));

                return $delta;
            }
        );
        $this->subject->addMigration($migration);

        $stack = [
            $this->buildConfig(['pkg-a' => '1.0.1']),                  // SYS:defaults
            $this->buildConfig([], ['parentField' => 'v']),             // P at implicit 1.0.0
            $this->buildConfig([], ['childField' => 'v']),              // C at implicit 1.0.0
        ];

        $this->subject->migrateStackInMemory($stack, ['pkg-a' => '1.0.1']);

        // Children-first: C (index 2) processed before P (index 1)
        // First call should be for the child entry, second for the parent
        self::assertCount(2, $migrationLog);
        self::assertSame(['childField'], $migrationLog[0], 'Child should be migrated first');
        self::assertSame(['parentField'], $migrationLog[1], 'Parent should be migrated second');
    }
}
