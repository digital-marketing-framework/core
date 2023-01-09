<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument;

use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
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

    /**
     * @param array<mixed> $configuration
     * @return array<array<mixed>>
     */
    public function getConfigurationStackFromConfiguration(array $configuration): array
    {
        // TODO: use an include array instead of one parent include?
        $processedDocumentIdentifiers = [];
        $result = [$configuration];
        while ($documentIdentifier = $configuration['parent'] ?? false) {
            $loopFound = in_array($documentIdentifier, $processedDocumentIdentifiers);
            $processedDocumentIdentifiers[] = $documentIdentifier;
            if ($loopFound) {
                throw new DigitalMarketingFrameworkException(sprintf('Configuration document reference loop found: %s', implode(',', $processedDocumentIdentifiers)));
            }

            $configuration = $this->getDocumentConfigurationFromIdentifier($documentIdentifier);
            if ($configuration === null) {
                throw new DigitalMarketingFrameworkException(sprintf('Configuration document not found, identifier: %s', $documentIdentifier));
            } else {
                $result[] = $configuration;
            }
        }
        return $result;
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
