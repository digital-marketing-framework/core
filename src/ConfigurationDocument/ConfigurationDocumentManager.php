<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\Exception\ConfigurationDocumentIncludeLoopException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;

class ConfigurationDocumentManager implements ConfigurationDocumentManagerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

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

    public function tidyDocument(string $document, SchemaDocument $schemaDocument): string
    {
        return $this->parser->tidyDocument($document, $schemaDocument);
    }

    public function saveDocument(string $documentIdentifier, string $document, SchemaDocument $schemaDocument): void
    {
        $document = $this->tidyDocument($document, $schemaDocument);
        $this->storage->setDocument($documentIdentifier, $document);
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
        $document = $this->parser->produceDocument($documentConfiguration, $schemaDocument);
        $this->saveDocument($documentIdentifier, $document, $schemaDocument);
    }

    public function deleteDocument(string $documentIdentifier): void
    {
        $this->storage->deleteDocument($documentIdentifier);
    }

    public function getDocumentIdentifierFromBaseName(string $baseName, bool $newFile = true): string
    {
        return $this->storage->getDocumentIdentiferFromBaseName($baseName, $newFile);
    }

    public function getDocumentInformation(string $documentIdentifier): array
    {
        $documentConfiguration = $this->getDocumentConfigurationFromIdentifier($documentIdentifier, true);
        return [
            'id' => $documentIdentifier,
            'shortId' => $this->storage->getShortIdentifier($documentIdentifier),
            'name' => $this->getName($documentConfiguration) ?: $documentIdentifier,
            'readonly' => $this->storage->isReadOnly($documentIdentifier),
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
        return $configuration[static::KEY_META_DATA][static::KEY_INCLUDES] ?? [];
    }

    public function setIncludes(array &$configuration, array $includes): void
    {
        $configuration[static::KEY_META_DATA][static::KEY_INCLUDES] = $includes;
    }

    public function getName(array $configuration): string
    {
        return $configuration[static::KEY_META_DATA][static::KEY_DOCUMENT_NAME] ?? '';
    }

    public function setName(array &$configuration, string $name): void
    {
        $configuration[static::KEY_META_DATA][static::KEY_DOCUMENT_NAME] = $name;
    }

    /**
     * @param array<string> $documentIdentifiers
     * @param array<string> $processedDocumentIdentifiers
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
     * @return array<array<mixed>>
     */
    public function getConfigurationStackFromConfiguration(array $configuration): array
    {
        $includedConfigurations = $this->getIncludedConfigurations($this->getIncludes($configuration));
        return [
            ...$includedConfigurations,
            $configuration
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

        $mergedConfiguration = $mergedConfiguration;
        $this->setIncludes($mergedConfiguration, $oldIncludes);
        $splitConfiguration = $this->splitConfiguration($mergedConfiguration);

        $this->setIncludes($splitConfiguration, $newIncludes);
        $configurationStack = $this->getConfigurationStackFromConfiguration($splitConfiguration);
        if ($inheritedConfigurationOnly) {
            array_pop($configurationStack);
        }
        return ConfigurationUtility::mergeConfigurationStack($configurationStack);
    }
}
