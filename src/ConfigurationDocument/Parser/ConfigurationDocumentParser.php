<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;

abstract class ConfigurationDocumentParser implements ConfigurationDocumentParserInterface, GlobalConfigurationAwareInterface, LoggerAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use LoggerAwareTrait;

    abstract public function parseDocument(string $document): array;

    /**
     * @param array<mixed> $configuration
     */
    abstract protected function doProduceDocument(array $configuration): string;

    public function produceDocument(array $configuration, ?SchemaDocument $schemaDocument = null): string
    {
        if ($schemaDocument instanceof SchemaDocument) {
            $schemaDocument->preSaveDataTransform($configuration);
        }

        return $this->doProduceDocument($configuration);
    }

    public function tidyDocument(string $document, ?SchemaDocument $schemaDocument = null): string
    {
        return $this->produceDocument($this->parseDocument($document), $schemaDocument);
    }
}
