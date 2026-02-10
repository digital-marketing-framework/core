<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\Exception\ConfigurationDocumentIncludeLoopException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationServiceInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\MigrationContext;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\ConfigurationStorageSettings;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Model\ConfigurationDocument\ConfigurationDocumentInformation;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Utility\ListUtility;

class ConfigurationDocumentManager implements ConfigurationDocumentManagerInterface, GlobalConfigurationAwareInterface, LoggerAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use LoggerAwareTrait;

    /** @var array<string, array<string,mixed>> */
    protected array $parsedConfigurationCache = [];

    protected bool $runtimeMigrationWarned = false;

    public function __construct(
        protected ConfigurationDocumentStorageInterface $storage,
        protected ConfigurationDocumentParserInterface $parser,
        protected ConfigurationDocumentStorageInterface $staticStorage,
        protected ConfigurationDocumentMigrationServiceInterface $migrationService,
    ) {
    }

    public function getStorage(): ConfigurationDocumentStorageInterface
    {
        return $this->storage;
    }

    public function getStaticStorage(): ConfigurationDocumentStorageInterface
    {
        return $this->staticStorage;
    }

    public function getParser(): ConfigurationDocumentParserInterface
    {
        return $this->parser;
    }

    public function getMigrationService(): ConfigurationDocumentMigrationServiceInterface
    {
        return $this->migrationService;
    }

    protected function getStorageForDocumentIdentifier(string $documentIdentifier): ConfigurationDocumentStorageInterface
    {
        if (preg_match('/^[A-Z]{2,}:/', $documentIdentifier)) {
            return $this->staticStorage;
        }

        return $this->storage;
    }

    public function tidyDocument(string $document, SchemaDocument $schemaDocument): string
    {
        return $this->parser->tidyDocument($document, $schemaDocument);
    }

    public function saveDocument(string $documentIdentifier, string $document, SchemaDocument $schemaDocument): void
    {
        $storage = $this->getStorageForDocumentIdentifier($documentIdentifier);
        $document = $this->tidyDocument($document, $schemaDocument);
        $storage->setDocument($documentIdentifier, $document);
    }

    protected function buildDocumentNameFromIdentifier(string $documentIdentifier): string
    {
        return $documentIdentifier;
    }

    public function createDocument(string $documentIdentifier, string $document, string $documentName, SchemaDocument $schemaDocument): void
    {
        if ($documentName === '') {
            $documentName = $this->buildDocumentNameFromIdentifier($documentIdentifier);
        }

        $documentConfiguration = $this->getDocumentConfigurationFromDocument($document);
        $this->setName($documentConfiguration, $documentName);
        $this->setVersion($documentConfiguration, $schemaDocument->getVersion());
        $document = $this->parser->produceDocument($documentConfiguration, $schemaDocument);
        $this->saveDocument($documentIdentifier, $document, $schemaDocument);
    }

    public function deleteDocument(string $documentIdentifier): void
    {
        $storage = $this->getStorageForDocumentIdentifier($documentIdentifier);
        $storage->deleteDocument($documentIdentifier);
    }

    public function getDocumentIdentifierFromBaseName(string $baseName, bool $newFile = true): string
    {
        return $this->storage->getDocumentIdentifierFromBaseName($baseName, $newFile);
    }

    public function getDocumentInformation(string $documentIdentifier): ConfigurationDocumentInformation
    {
        $storage = $this->getStorageForDocumentIdentifier($documentIdentifier);
        $documentConfiguration = $this->getDocumentConfigurationFromIdentifier($documentIdentifier, true);
        $name = $this->getName($documentConfiguration);

        return new ConfigurationDocumentInformation(
            $documentIdentifier,
            $storage->getShortIdentifier($documentIdentifier),
            $name !== '' ? $name : $documentIdentifier,
            $storage->isReadOnly($documentIdentifier),
            $this->getIncludes($documentConfiguration)
        );
    }

    /**
     * @return array<string>
     */
    public function getDocumentIdentifiers(): array
    {
        $identifiers = $this->staticStorage->getDocumentIdentifiers();
        foreach ($this->storage->getDocumentIdentifiers() as $identifier) {
            if (!in_array($identifier, $identifiers, true)) {
                $identifiers[] = $identifier;
            }
        }

        return $identifiers;
    }

    /**
     * @return array<string,mixed>
     */
    public function getDocumentConfigurationFromDocument(string $document): array
    {
        if ($document === '') {
            return [];
        }

        $cacheKey = md5($document);
        if (!isset($this->parsedConfigurationCache[$cacheKey])) {
            $this->parsedConfigurationCache[$cacheKey] = $this->parser->parseDocument($document);
        }

        return $this->parsedConfigurationCache[$cacheKey];
    }

    public function getDocumentFromIdentifier(string $documentIdentifier, bool $metaDataOnly = false): string
    {
        $document = $this->staticStorage->getDocument($documentIdentifier, $metaDataOnly);
        if ($document !== '') {
            return $document;
        }

        return $this->storage->getDocument($documentIdentifier, $metaDataOnly);
    }

    /**
     * @return array<string,mixed>
     */
    public function getDocumentConfigurationFromIdentifier(string $documentIdentifier, bool $metaDataOnly = false): array
    {
        return $this->getDocumentConfigurationFromDocument(
            $this->getDocumentFromIdentifier($documentIdentifier, $metaDataOnly)
        );
    }

    public function getIncludes(array $configuration): array
    {
        $includeList = $configuration[static::KEY_META_DATA][static::KEY_INCLUDES] ?? [];

        return ListUtility::flatten($includeList);
    }

    public function setIncludes(array &$configuration, array $includes): void
    {
        $includeList = [];
        $includeList = ListUtility::appendMultiple($includeList, $includes);
        $configuration[static::KEY_META_DATA][static::KEY_INCLUDES] = $includeList;
    }

    public function getName(array $configuration): string
    {
        return $configuration[static::KEY_META_DATA][static::KEY_DOCUMENT_NAME] ?? '';
    }

    public function setName(array &$configuration, string $name): void
    {
        $configuration[static::KEY_META_DATA][static::KEY_DOCUMENT_NAME] = $name;
    }

    public function getVersion(array $configuration): array
    {
        return $configuration[static::KEY_META_DATA][static::KEY_DOCUMENT_VERSION] ?? [];
    }

    public function setVersion(array &$configuration, array $version): void
    {
        $configuration[static::KEY_META_DATA][static::KEY_DOCUMENT_VERSION] = $version;
    }

    public function getVersionByKey(array $configuration, string $key): string
    {
        return $this->getVersion($configuration)[$key] ?? '';
    }

    public function setVersionByKey(array &$configuration, string $key, string $version): void
    {
        $configuration[static::KEY_META_DATA][static::KEY_DOCUMENT_VERSION][$key] = $version;
    }

    public function unsetVersionByKey(array &$configuration, string $key): void
    {
        unset($configuration[static::KEY_META_DATA][static::KEY_DOCUMENT_VERSION][$key]);
    }

    /**
     * @param array<string> $documentIdentifiers
     * @param array<string> $allDocumentIdentifiers
     * @param array<string> $processedDocumentIdentifiers
     *
     * @return array<array<mixed>>
     */
    protected function getIncludedConfigurations(array $documentIdentifiers, array &$allDocumentIdentifiers = [], array $processedDocumentIdentifiers = []): array
    {
        $includes = [];
        foreach ($documentIdentifiers as $documentIdentifier) {
            $subProcessedDocumentIdentifiers = $processedDocumentIdentifiers;
            $subProcessedDocumentIdentifiers[] = $documentIdentifier;

            if (in_array($documentIdentifier, $processedDocumentIdentifiers, true)) {
                throw new ConfigurationDocumentIncludeLoopException(sprintf('Configuration document reference loop found: %s', implode(',', $subProcessedDocumentIdentifiers)));
            }

            // NOTE when building a configuration stack, we only include each document once
            if (in_array($documentIdentifier, $allDocumentIdentifiers, true)) {
                continue;
            }

            $configuration = $this->getDocumentConfigurationFromIdentifier($documentIdentifier);
            $subConfigurations = $this->getIncludedConfigurations($this->getIncludes($configuration), $allDocumentIdentifiers, $subProcessedDocumentIdentifiers);
            array_push($includes, ...$subConfigurations);
            $includes[] = $configuration;
            $allDocumentIdentifiers[] = $documentIdentifier;
        }

        return $includes;
    }

    /**
     * @param array<mixed> $configuration
     *
     * @return array<array<mixed>>
     */
    public function getConfigurationStackFromConfiguration(array $configuration, ?SchemaDocument $schemaDocument = null, bool $migrateInMemory = true): array
    {
        $includes = $this->getIncludes($configuration);
        array_unshift($includes, 'SYS:defaults');
        $includedConfigurations = $this->getIncludedConfigurations($includes);

        $stack = [
            ...$includedConfigurations,
            $configuration,
        ];

        if ($schemaDocument instanceof SchemaDocument && $migrateInMemory) {
            $this->warnIfOutdated($stack, $schemaDocument);
            $this->migrationService->migrateStackInMemory($stack, $schemaDocument->getVersion());
        }

        return $stack;
    }

    /**
     * @return array<array<string,mixed>>
     */
    public function getConfigurationStackFromDocument(string $document, ?SchemaDocument $schemaDocument = null, bool $migrateInMemory = true): array
    {
        $configuration = $this->parser->parseDocument($document);

        return $this->getConfigurationStackFromConfiguration($configuration, $schemaDocument, $migrateInMemory);
    }

    /**
     * @return array<array<string,mixed>>
     */
    public function getConfigurationStackFromIdentifier(string $documentIdentifier, ?SchemaDocument $schemaDocument = null, bool $migrateInMemory = true): array
    {
        $document = $this->storage->getDocument($documentIdentifier);

        return $this->getConfigurationStackFromDocument($document, $schemaDocument, $migrateInMemory);
    }

    public function getDefaultConfigurationIdentifier(): string
    {
        return $this->globalConfiguration->getGlobalSettings(ConfigurationStorageSettings::class)->getDefaultConfigurationDocument();
    }

    public function getDefaultConfigurationStack(?SchemaDocument $schemaDocument = null, bool $migrateInMemory = true): array
    {
        $documentIdentifier = $this->getDefaultConfigurationIdentifier();
        if ($documentIdentifier === '') {
            throw new DigitalMarketingFrameworkException('No default configuration document given');
        }

        return $this->getConfigurationStackFromIdentifier($documentIdentifier, $schemaDocument, $migrateInMemory);
    }

    /**
     * Log a single warning per process if any configuration in the stack is outdated.
     *
     * @param array<array<string, mixed>> $stack
     */
    protected function warnIfOutdated(array $stack, SchemaDocument $schemaDocument): void
    {
        if ($this->runtimeMigrationWarned) {
            return;
        }

        for ($i = 1, $count = count($stack); $i < $count; ++$i) {
            if ($this->migrationService->outdated($stack[$i], $schemaDocument)) {
                $this->runtimeMigrationWarned = true;
                $this->logger->warning('Outdated configuration documents were migrated in-memory. Run "anyrel:migrate" or use the upgrade wizard to update permanently.');

                return;
            }
        }
    }

    // -- Migration facade methods (delegate to MigrationService) --

    public function addMigration(ConfigurationDocumentMigrationInterface $migration): void
    {
        $this->migrationService->addMigration($migration);
    }

    public function migrate(array $configuration, SchemaDocument $schemaDocument): array
    {
        $includes = $this->getIncludes($configuration);
        $sysDefaultsConfig = $this->getDocumentConfigurationFromIdentifier('SYS:defaults');
        $parentConfigurations = $this->getIncludedConfigurations($includes);

        $context = new MigrationContext($parentConfigurations, $sysDefaultsConfig);

        return $this->migrationService->migrateConfiguration(
            $configuration,
            $context,
            $schemaDocument
        );
    }

    public function outdated(array $configuration, SchemaDocument $schemaDocument): bool
    {
        return $this->migrationService->outdated($configuration, $schemaDocument);
    }

    public function genuinelyOutdated(array $configuration, SchemaDocument $schemaDocument): bool
    {
        // Quick check first
        if (!$this->migrationService->outdated($configuration, $schemaDocument)) {
            return false;
        }

        // Build context from includes
        $includes = $this->getIncludes($configuration);
        $sysDefaultsConfig = $this->getDocumentConfigurationFromIdentifier('SYS:defaults');
        $parentConfigurations = $this->getIncludedConfigurations($includes);

        $context = new MigrationContext($parentConfigurations, $sysDefaultsConfig);

        return $this->migrationService->genuinelyOutdated($configuration, $context, $schemaDocument);
    }
}
