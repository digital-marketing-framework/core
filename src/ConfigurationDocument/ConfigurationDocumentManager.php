<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\Exception\ConfigurationDocumentIncludeLoopException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;

class ConfigurationDocumentManager implements ConfigurationDocumentManagerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const KEY_DOCUMENT_NAME = 'name';

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

    public function tidyDocument(string $document): string
    {
        return $this->parser->tidyDocument($document);
    }

    public function saveDocument(string $documentIdentifier, string $document): void
    {
        $document = $this->tidyDocument($document);
        $this->storage->setDocument($documentIdentifier, $document);
    }

    protected function buildDocumentNameFromIdentifier(string $documentIdentifier): string
    {
        return $documentIdentifier;
    }

    public function createDocument(string $documentIdentifier, string $document, string $documentName = ''): void
    {
        if ($documentName === '') {
            $documentName = $this->buildDocumentNameFromIdentifier($documentIdentifier);
        }
        $documentConfiguration = $this->getDocumentConfigurationFromDocument($document);
        $documentConfiguration[static::KEY_DOCUMENT_NAME] = $documentName;
        $document = $this->parser->produceDocument($documentConfiguration);
        $this->saveDocument($documentIdentifier, $document);
    }

    public function deleteDocument(string $documentIdentifier): void
    {
        $this->storage->deleteDocument($documentIdentifier);
    }

    public function getDocumentIdentifierFromBaseName(string $baseName, bool $newFile = true): string
    {
        return $this->storage->getDocumentIdentiferFromBaseName($baseName, $newFile);
    }

    public function getMetaDataFromIdentifier(string $documentIdentifier): array
    {
        $documentConfiguration = $this->getDocumentConfigurationFromIdentifier($documentIdentifier);
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

    public function getDocumentFromIdentifier(string $documentIdentifier): string
    {
        $document = $this->staticStorage?->getDocument($documentIdentifier);
        if ($document !== null && $document !== '') {
            return $document;
        }
        return $this->storage->getDocument($documentIdentifier);
    }

    /**
     * @return array<mixed>
     */
    public function getDocumentConfigurationFromIdentifier(string $documentIdentifier): array
    {
        return $this->getDocumentConfigurationFromDocument(
            $this->getDocumentFromIdentifier($documentIdentifier)
        );
    }

    protected function getIncludes(array $configuration): array
    {
        if (
            isset($configuration[static::KEY_INCLUDES])
            && is_array($configuration[static::KEY_INCLUDES])
            && !empty($configuration[static::KEY_INCLUDES])
        ) {
            return $configuration[static::KEY_INCLUDES];
        }
        return [];
    }

    protected function getName(array $configuration): string
    {
        return $configuration[static::KEY_DOCUMENT_NAME] ?? '';
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
}
