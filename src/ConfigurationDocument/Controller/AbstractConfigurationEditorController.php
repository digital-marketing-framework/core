<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareTrait;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorAwareInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorAwareTrait;

abstract class AbstractConfigurationEditorController implements ConfigurationEditorControllerInterface, SchemaProcessorAwareInterface, ConfigurationDocumentParserAwareInterface
{
    use SchemaProcessorAwareTrait;
    use ConfigurationDocumentParserAwareTrait;

    protected SchemaDocument $schemaDocument;

    public function getSchemaDocument(): SchemaDocument
    {
        return $this->schemaDocument;
    }

    public function setSchemaDocument(SchemaDocument $schemaDocument): void
    {
        $this->schemaDocument = $schemaDocument;
    }

    public function getDefaultConfiguration(): array
    {
        return $this->schemaProcessor->getDefaultValue($this->getSchemaDocument());
    }

    public function preSaveDataTransform(mixed &$data): void
    {
        $this->schemaProcessor->preSaveDataTransform($this->getSchemaDocument(), $data);
    }

    public function convertValueTypes(mixed &$data): void
    {
        $this->schemaProcessor->convertValueTypes($this->getSchemaDocument(), $data);
    }

    public function getSchemaDocumentAsArray(): array
    {
        return $this->getSchemaDocument()->toArray();
    }

    public function parseDocument(string $document): array
    {
        return $this->configurationDocumentParser->parseDocument($document);
    }

    public function produceDocument(array $configuration): string
    {
        return $this->configurationDocumentParser->produceDocument($configuration, $this->getSchemaDocument());
    }
}
