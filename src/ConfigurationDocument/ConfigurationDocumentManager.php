<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\Exception\ConfigurationDocumentIncludeLoopException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\FatalMigrationException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\MigrationException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;
use DigitalMarketingFramework\Core\Utility\ListUtility;

class ConfigurationDocumentManager implements ConfigurationDocumentManagerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var array<string,array<string,ConfigurationDocumentMigrationInterface>> */
    protected array $migrations = [];

    public function __construct(
        protected ConfigurationDocumentStorageInterface $storage,
        protected ConfigurationDocumentParserInterface $parser,
        protected ?ConfigurationDocumentStorageInterface $staticStorage = null,
    ) {
    }

    public function getStorage(): ConfigurationDocumentStorageInterface
    {
        return $this->storage;
    }

    public function getStaticStorage(): ?ConfigurationDocumentStorageInterface
    {
        return $this->staticStorage;
    }

    public function getParser(): ConfigurationDocumentParserInterface
    {
        return $this->parser;
    }

    protected function getStorageForDocumentIdentifier(string $documentIdentifier): ConfigurationDocumentStorageInterface
    {
        if (preg_match('/^[A-Z]{2,}:/', $documentIdentifier)) {
            if (!$this->staticStorage instanceof ConfigurationDocumentStorageInterface) {
                throw new DigitalMarketingFrameworkException('No static document storage found.');
            }

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
        return $this->storage->getDocumentIdentiferFromBaseName($baseName, $newFile);
    }

    public function getDocumentInformation(string $documentIdentifier): array
    {
        $storage = $this->getStorageForDocumentIdentifier($documentIdentifier);
        $documentConfiguration = $this->getDocumentConfigurationFromIdentifier($documentIdentifier, true);

        return [
            'id' => $documentIdentifier,
            'shortId' => $storage->getShortIdentifier($documentIdentifier),
            'name' => $this->getName($documentConfiguration) ?: $documentIdentifier,
            'readonly' => $storage->isReadOnly($documentIdentifier),
            'includes' => $this->getIncludes($documentConfiguration),
        ];
    }

    /**
     * @return array<string>
     */
    public function getDocumentIdentifiers(): array
    {
        $identifiers = $this->staticStorage?->getDocumentIdentifiers() ?? [];
        foreach ($this->storage->getDocumentIdentifiers() as $identifier) {
            if (!in_array($identifier, $identifiers)) {
                $identifiers[] = $identifier;
            }
        }

        return $identifiers;
    }

    /**
     * @return array<mixed>
     */
    public function getDocumentConfigurationFromDocument(string $document): array
    {
        return $this->parser->parseDocument($document);
    }

    public function getDocumentFromIdentifier(string $documentIdentifier, bool $metaDataOnly = false): string
    {
        $document = $this->staticStorage?->getDocument($documentIdentifier, $metaDataOnly);
        if ($document !== null && $document !== '') {
            return $document;
        }

        return $this->storage->getDocument($documentIdentifier, $metaDataOnly);
    }

    /**
     * @return array<mixed>
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
     * @param array<string> $processedDocumentIdentifiers
     *
     * @return array<array<mixed>>
     */
    protected function getIncludedConfigurations(array $documentIdentifiers, array $processedDocumentIdentifiers = []): array
    {
        $includes = [];
        foreach ($documentIdentifiers as $documentIdentifier) {
            $subProcessedDocumentIdentifiers = $processedDocumentIdentifiers;
            $subProcessedDocumentIdentifiers[] = $documentIdentifier;

            if (in_array($documentIdentifier, $processedDocumentIdentifiers)) {
                throw new ConfigurationDocumentIncludeLoopException(sprintf('Configuration document reference loop found: %s', implode(',', $subProcessedDocumentIdentifiers)));
            }

            $configuration = $this->getDocumentConfigurationFromIdentifier($documentIdentifier);
            $subConfigurations = $this->getIncludedConfigurations($this->getIncludes($configuration), $subProcessedDocumentIdentifiers);
            array_push($includes, ...$subConfigurations);
            $includes[] = $configuration;
        }

        return $includes;
    }

    /**
     * @param array<mixed> $configuration
     *
     * @return array<array<mixed>>
     */
    public function getConfigurationStackFromConfiguration(array $configuration): array
    {
        // TODO Trigger migrations here?
        //      Should each configuration be migrated with its parents or on its own?
        //      What if some version cannot be reached? Distringuish between fatal issues and non-fatal issues?
        $includes = $this->getIncludes($configuration);
        array_unshift($includes, 'SYS:defaults');
        $includedConfigurations = $this->getIncludedConfigurations($includes);

        return [
            ...$includedConfigurations,
            $configuration,
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function getConfigurationStackFromDocument(string $document): array
    {
        $configuration = $this->parser->parseDocument($document);

        return $this->getConfigurationStackFromConfiguration($configuration);
    }

    /**
     * @return array<array<mixed>>
     */
    public function getConfigurationStackFromIdentifier(string $documentIdentifier): array
    {
        $document = $this->storage->getDocument($documentIdentifier);

        return $this->getConfigurationStackFromDocument($document);
    }

    public function splitConfiguration(array $mergedConfiguration): array
    {
        $configurationStack = $this->getConfigurationStackFromConfiguration($mergedConfiguration);
        array_pop($configurationStack);
        $parentConfiguration = ConfigurationUtility::mergeConfigurationStack($configurationStack);

        return ConfigurationUtility::splitConfiguration($parentConfiguration, $mergedConfiguration);
    }

    public function mergeConfiguration(array $configuration, bool $inheritedConfigurationOnly = false): array
    {
        $configurationStack = $this->getConfigurationStackFromConfiguration($configuration);
        if ($inheritedConfigurationOnly) {
            array_pop($configurationStack);
        }

        return ConfigurationUtility::mergeConfigurationStack($configurationStack);
    }

    public function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array
    {
        $oldIncludes = $this->getIncludes($referenceMergedConfiguration);
        $newIncludes = $this->getIncludes($mergedConfiguration);
        $this->setIncludes($mergedConfiguration, $oldIncludes);
        $splitConfiguration = $this->splitConfiguration($mergedConfiguration);

        $this->setIncludes($splitConfiguration, $newIncludes);
        $configurationStack = $this->getConfigurationStackFromConfiguration($splitConfiguration);
        if ($inheritedConfigurationOnly) {
            array_pop($configurationStack);
        }

        return ConfigurationUtility::mergeConfigurationStack($configurationStack);
    }

    public function addMigration(ConfigurationDocumentMigrationInterface $migration): void
    {
        $this->migrations[$migration->getKey()][$migration->getSourceVersion()] = $migration;
    }

    /**
     * @param array<string,mixed> $configuration
     */
    protected function migrateByKey(array &$configuration, string $key, string $targetVersion): void
    {
        $version = $this->getVersionByKey($configuration, $key);
        while ($version !== $targetVersion) {
            if (isset($this->migrations[$key][$version])) {
                // migration found
                try {
                    $migration = $this->migrations[$key][$version];
                    if (!$migration->checkVersions()) {
                        throw new FatalMigrationException(sprintf('Migration source version "%s" seems to be bigger than or equal to target version "%s".', $migration->getSourceVersion(), $migration->getTargetVersion()));
                    }

                    $configuration = $migration->migrate($configuration);
                    $version = $migration->getTargetVersion();
                } catch (MigrationException $e) {
                    // a non fatal migration exception aborts the migration for this key only
                    // a fatal migration exception is not caught by the migration process at all
                    $this->logger->warning($e->getMessage());
                    break;
                }
            } else {
                // no migration found
                // TODO now what? i guess the version mismatch can be detected at a later point
                break;
            }
        }

        if ($version === '') {
            // if there is no initial migration present
            // assume that none is necessary to get from no verison to the current version
            $this->setVersionByKey($configuration, $key, $targetVersion);
        } else {
            $this->setVersionByKey($configuration, $key, $version);
        }
    }

    public function migrate(array $configuration, SchemaDocument $schemaDocument): array
    {
        $schemaVersion = $schemaDocument->getVersion();
        foreach ($schemaVersion as $key => $targetVersion) {
            if ($this->getVersionByKey($configuration, $key) !== $targetVersion) {
                // document does not have a version key or the version does not match
                $this->migrateByKey($configuration, $key, $targetVersion);
            }
        }

        foreach (array_keys($this->getVersion($configuration)) as $key) {
            if (!isset($schemaVersion[$key])) {
                // document has a version key that does not exist in the current schema
                // TODO should the version really be unset in this case?
                $this->unsetVersionByKey($configuration, $key);
            }
        }

        return $configuration;
    }

    public function outdated(array $configuration, SchemaDocument $schemaDocument): bool
    {
        $schemaVersion = $schemaDocument->getVersion();
        foreach ($schemaVersion as $key => $targetVersion) {
            if ($this->getVersionByKey($configuration, $key) !== $targetVersion) {
                return true;
            }
        }

        foreach (array_keys($this->getVersion($configuration)) as $key) {
            if (!isset($schemaVersion[$key])) {
                return true;
            }
        }

        return false;
    }
}
