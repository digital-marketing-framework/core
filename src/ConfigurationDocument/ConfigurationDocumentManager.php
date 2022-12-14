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

    public function __construct(
        protected ConfigurationDocumentStorageInterface $storage,
        protected ConfigurationDocumentParserInterface $parser,
    ) {
    }

    public function getStorage(): ConfigurationDocumentStorageInterface
    {
        return $this->storage;
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

    /**
     * @return array<string>
     */
    public function getDocumentIdentifiers(): array
    {
        return $this->storage->getDocumentIdentifiers();
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
            $configuration,
            ...$includedConfigurations
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
