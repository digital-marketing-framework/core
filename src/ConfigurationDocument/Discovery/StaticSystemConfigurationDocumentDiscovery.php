<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Discovery;

use BadMethodCallException;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareTrait;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorAwareInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorAwareTrait;

abstract class StaticSystemConfigurationDocumentDiscovery implements StaticConfigurationDocumentDiscoveryInterface, ConfigurationDocumentParserAwareInterface, SchemaProcessorAwareInterface
{
    use ConfigurationDocumentParserAwareTrait;
    use SchemaProcessorAwareTrait;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    abstract public function getIdentifiers(): array;

    /**
     * @return array<string,mixed>
     */
    abstract protected function buildContent(string $identifier, SchemaDocument $schemaDocument): array;

    abstract protected function getConfigurationDocumentName(string $identifier): string;

    public function getContent(string $identifier, bool $metaDataOnly = false): ?string
    {
        if (!$this->exists($identifier)) {
            return null;
        }

        $schemaDocument = null;
        $config = [];
        $metaData = $this->buildMetaData($this->getConfigurationDocumentName($identifier));

        if (!$metaDataOnly) {
            $schemaDocument = $this->registry->getRegistryCollection()->getConfigurationSchemaDocument();
            $config = $this->buildContent($identifier, $schemaDocument);
        }

        return $this->configurationDocumentParser->produceDocument($metaData + $config, $schemaDocument);
    }

    public function match(string $identifier): bool
    {
        return $this->exists($identifier);
    }

    public function exists(string $identifier): bool
    {
        return in_array($identifier, $this->getIdentifiers(), true);
    }

    public function isReadonly(string $identifier): bool
    {
        return true;
    }

    public function setContent(string $identifier, string $content): void
    {
        throw new BadMethodCallException('System configuration cannot be saved or overwritten.');
    }

    /**
     * @return array{metaData:array{name:string}}
     */
    protected function buildMetaData(string $documentName): array
    {
        return [
            ConfigurationDocumentManagerInterface::KEY_META_DATA => [
                ConfigurationDocumentManagerInterface::KEY_DOCUMENT_NAME => $documentName,
            ],
        ];
    }
}
