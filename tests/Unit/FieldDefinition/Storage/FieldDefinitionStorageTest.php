<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\FieldDefinition\Storage;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\FieldDefinition\Storage\FieldDefinitionStorage;
use DigitalMarketingFramework\Core\FileStorage\FileStorageInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\ConfigurationStorageSettings;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Resource\ResourceServiceInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FieldDefinitionStorageTest extends TestCase
{
    protected const PACKAGE_FOLDER = 'PKG:vendor/package/res/fields';

    protected const SITE_FOLDER = 'PKG:vendor/site/res/fields';

    protected const WRITABLE_FOLDER = '1:/anyrel/fields';

    /** @var array<string,string> resource identifier => content */
    protected array $resourceFiles = [];

    /** @var array<string,string> file identifier => content */
    protected array $storedFiles = [];

    protected RegistryInterface&MockObject $registry;

    protected FieldDefinitionStorage $subject;

    protected function setUp(): void
    {
        $this->resourceFiles = [
            self::PACKAGE_FOLDER . '/distributor.out.defaults.salesforce.lead.fields.yaml' => 'package lead',
            self::PACKAGE_FOLDER . '/collector.in.defaults.pardot.form.fields.yaml' => 'package form',
            self::SITE_FOLDER . '/distributor.out.defaults.salesforce.lead.fields.yaml' => 'site lead',
            self::PACKAGE_FOLDER . '/not-a-definition.yaml' => 'ignored',
        ];
        $this->storedFiles = [
            self::WRITABLE_FOLDER . '/distributor.out.defaults.salesforce.lead.fields.yaml' => 'writable lead',
            self::WRITABLE_FOLDER . '/distributor.out.defaults.mail.contact.fields.yaml' => 'writable contact',
        ];

        $this->registry = $this->createMock(RegistryInterface::class);
        $this->registry->method('getFieldDefinitionFolderIdentifiers')
            ->willReturn([self::PACKAGE_FOLDER, self::SITE_FOLDER]);
        $this->registry->method('getResourceService')
            ->willReturnCallback(fn (string $identifier): ?ResourceServiceInterface => str_starts_with($identifier, 'PKG:') ? $this->resourceService() : null);
    }

    protected function resourceService(): ResourceServiceInterface&MockObject
    {
        $service = $this->createMock(ResourceServiceInterface::class);
        $service->method('getFilesInResourceFolder')->willReturnCallback(function (string $folder): array {
            $names = [];
            foreach (array_keys($this->resourceFiles) as $identifier) {
                if (str_starts_with($identifier, $folder . '/')) {
                    $names[] = basename($identifier);
                }
            }

            return $names;
        });
        $service->method('resourceExists')->willReturnCallback(fn (string $i): bool => isset($this->resourceFiles[$i]));
        $service->method('getResourceContent')->willReturnCallback(fn (string $i): ?string => $this->resourceFiles[$i] ?? null);
        $service->method('setResourceContent')->willReturnCallback(function (string $i, string $c): bool {
            $this->resourceFiles[$i] = $c;

            return true;
        });
        $service->method('deleteResource')->willReturnCallback(function (string $i): bool {
            unset($this->resourceFiles[$i]);

            return true;
        });

        return $service;
    }

    protected function fileStorage(): FileStorageInterface&MockObject
    {
        $storage = $this->createMock(FileStorageInterface::class);
        $storage->method('folderExists')->willReturn(true);
        $storage->method('getFilesFromFolder')->willReturnCallback(function (string $folder): array {
            $found = [];
            foreach (array_keys($this->storedFiles) as $identifier) {
                if (str_starts_with($identifier, $folder . '/')) {
                    $found[] = $identifier;
                }
            }

            return $found;
        });
        $storage->method('getFileName')->willReturnCallback(static fn (string $i): string => basename($i));
        $storage->method('fileExists')->willReturnCallback(fn (string $i): bool => isset($this->storedFiles[$i]));
        $storage->method('getFileContents')->willReturnCallback(fn (string $i): ?string => $this->storedFiles[$i] ?? null);
        $storage->method('putFileContents')->willReturnCallback(function (string $i, string $c): void {
            $this->storedFiles[$i] = $c;
        });
        $storage->method('deleteFile')->willReturnCallback(function (string $i): void {
            unset($this->storedFiles[$i]);
        });

        return $storage;
    }

    protected function createSubject(string $writableFolder = self::WRITABLE_FOLDER, bool $allowSaveToPackages = false): FieldDefinitionStorage
    {
        $configurationSettings = new ConfigurationStorageSettings();
        $configurationSettings->injectSettings([
            CoreGlobalConfigurationSchema::KEY_CONFIGURATION_STORAGE_ALLOW_SAVE_TO_EXTENSION_PATHS => $allowSaveToPackages,
        ]);

        $globalConfiguration = $this->createMock(GlobalConfigurationInterface::class);
        $globalConfiguration->method('getGlobalSettings')->willReturn($configurationSettings);

        // The storage folder is read raw, as it is while services are being registered.
        $globalConfiguration->method('get')->willReturn([
            CoreGlobalConfigurationSchema::KEY_FIELD_DEFINITION_STORAGE => [
                CoreGlobalConfigurationSchema::KEY_FIELD_DEFINITION_STORAGE_FOLDER => $writableFolder,
                CoreGlobalConfigurationSchema::KEY_FIELD_DEFINITION_STORAGE_ADDITIONAL_FOLDERS => '',
            ],
        ]);

        $subject = new FieldDefinitionStorage($this->registry);
        $subject->setFileStorage($this->fileStorage());
        $subject->setGlobalConfiguration($globalConfiguration);

        return $subject;
    }

    #[Test]
    public function everyContextWithAFileIsListedOnce(): void
    {
        $identifiers = $this->createSubject()->getContextIdentifiers();

        sort($identifiers);
        $this->assertSame([
            'collector.in.defaults.pardot.form',
            'distributor.out.defaults.mail.contact',
            'distributor.out.defaults.salesforce.lead',
        ], $identifiers);
    }

    #[Test]
    public function filesWithoutTheFieldsSuffixAreIgnored(): void
    {
        $this->assertNotContains('not-a-definition', $this->createSubject()->getContextIdentifiers());
    }

    #[Test]
    public function foldersHoldingAContextAreListedInPrecedenceOrder(): void
    {
        $this->assertSame(
            [self::PACKAGE_FOLDER, self::SITE_FOLDER, self::WRITABLE_FOLDER],
            $this->createSubject()->getFolders('distributor.out.defaults.salesforce.lead')
        );
        $this->assertSame(
            [self::PACKAGE_FOLDER],
            $this->createSubject()->getFolders('collector.in.defaults.pardot.form')
        );
    }

    #[Test]
    public function readingWithoutAFolderTakesTheHighestPrecedenceLayer(): void
    {
        $this->assertSame('writable lead', $this->createSubject()->getContent('distributor.out.defaults.salesforce.lead'));
    }

    #[Test]
    public function readingWithAFolderTakesThatLayer(): void
    {
        $subject = $this->createSubject();

        $this->assertSame('package lead', $subject->getContent('distributor.out.defaults.salesforce.lead', self::PACKAGE_FOLDER));
        $this->assertSame('site lead', $subject->getContent('distributor.out.defaults.salesforce.lead', self::SITE_FOLDER));
    }

    #[Test]
    public function readingAnUnknownContextGivesNull(): void
    {
        $subject = $this->createSubject();

        $this->assertNull($subject->getContent('nope'));
        $this->assertFalse($subject->exists('nope'));
        $this->assertFalse($subject->exists('collector.in.defaults.pardot.form', self::WRITABLE_FOLDER));
        $this->assertTrue($subject->exists('collector.in.defaults.pardot.form'));
    }

    #[Test]
    public function onlyTheWritableFolderIsWritableByDefault(): void
    {
        $this->assertSame([self::WRITABLE_FOLDER], $this->createSubject()->getWritableFolders());
    }

    #[Test]
    public function packageFoldersBecomeWritableWhenSavingToPackagesIsAllowed(): void
    {
        $this->assertSame(
            [self::PACKAGE_FOLDER, self::SITE_FOLDER, self::WRITABLE_FOLDER],
            $this->createSubject(allowSaveToPackages: true)->getWritableFolders()
        );
    }

    #[Test]
    public function writingWithoutAFolderUsesTheWritableFolder(): void
    {
        $subject = $this->createSubject();
        $subject->setContent('distributor.out.defaults.new.thing', 'fresh');

        $this->assertSame('fresh', $this->storedFiles[self::WRITABLE_FOLDER . '/distributor.out.defaults.new.thing.fields.yaml'] ?? null);
    }

    /**
     * Regression: the write target used to be derived from wherever the context already
     * existed, so adding this project's custom fields would have written them into the
     * package that happened to ship the context. Allowing writes to package paths says
     * *may write into packages*, not *which* package.
     */
    #[Test]
    public function writingNeverFallsBackToTheFolderAContextAlreadyLivesIn(): void
    {
        $subject = $this->createSubject(allowSaveToPackages: true);
        $subject->setContent('collector.in.defaults.pardot.form', 'mine');

        $this->assertSame('package form', $this->resourceFiles[self::PACKAGE_FOLDER . '/collector.in.defaults.pardot.form.fields.yaml']);
        $this->assertSame('mine', $this->storedFiles[self::WRITABLE_FOLDER . '/collector.in.defaults.pardot.form.fields.yaml'] ?? null);
    }

    #[Test]
    public function writingIntoAChosenPackageFolderIsAllowedOnADevelopmentSystem(): void
    {
        $subject = $this->createSubject(allowSaveToPackages: true);
        $subject->setContent('distributor.out.defaults.salesforce.lead', 'authored', self::SITE_FOLDER);

        $this->assertSame('authored', $this->resourceFiles[self::SITE_FOLDER . '/distributor.out.defaults.salesforce.lead.fields.yaml']);
        $this->assertSame('package lead', $this->resourceFiles[self::PACKAGE_FOLDER . '/distributor.out.defaults.salesforce.lead.fields.yaml']);
    }

    #[Test]
    public function writingIntoAPackageFolderIsRefusedOtherwise(): void
    {
        $this->expectException(DigitalMarketingFrameworkException::class);

        $this->createSubject()->setContent('distributor.out.defaults.salesforce.lead', 'nope', self::PACKAGE_FOLDER);
    }

    #[Test]
    public function writingIsRefusedWhenNoWritableFolderIsConfigured(): void
    {
        $this->expectException(DigitalMarketingFrameworkException::class);

        $this->createSubject(writableFolder: '')->setContent('distributor.out.defaults.new.thing', 'nope');
    }

    #[Test]
    public function aContextIsEditableWhileAnyLayerIsWritable(): void
    {
        // The package's own file cannot be written, but a definition in the writable folder
        // would take precedence over it.
        $this->assertFalse($this->createSubject()->isReadOnly('collector.in.defaults.pardot.form'));
        $this->assertTrue($this->createSubject()->isReadOnly('collector.in.defaults.pardot.form', self::PACKAGE_FOLDER));
        $this->assertFalse($this->createSubject(allowSaveToPackages: true)->isReadOnly('collector.in.defaults.pardot.form', self::PACKAGE_FOLDER));
    }

    #[Test]
    public function nothingIsEditableWithoutAWritableFolder(): void
    {
        $this->assertTrue($this->createSubject(writableFolder: '')->isReadOnly('collector.in.defaults.pardot.form'));
    }

    #[Test]
    public function deletingRemovesTheLayerItWasAskedFor(): void
    {
        $subject = $this->createSubject();
        $subject->delete('distributor.out.defaults.salesforce.lead');

        $this->assertArrayNotHasKey(self::WRITABLE_FOLDER . '/distributor.out.defaults.salesforce.lead.fields.yaml', $this->storedFiles);
        $this->assertSame('package lead', $this->resourceFiles[self::PACKAGE_FOLDER . '/distributor.out.defaults.salesforce.lead.fields.yaml']);
    }

    #[Test]
    public function deletingLeavesReadOnlyLayersAlone(): void
    {
        $subject = $this->createSubject();
        $subject->delete('distributor.out.defaults.salesforce.lead', self::PACKAGE_FOLDER);

        $this->assertSame('package lead', $this->resourceFiles[self::PACKAGE_FOLDER . '/distributor.out.defaults.salesforce.lead.fields.yaml']);
    }
}
