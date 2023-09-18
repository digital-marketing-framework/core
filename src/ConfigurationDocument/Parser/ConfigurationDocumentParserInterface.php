<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

interface ConfigurationDocumentParserInterface
{
    /**
     * @return array<mixed>
     */
    public function parseDocument(string $document): array;

    /**
     * @param array<mixed> $configuration
     */
    public function produceDocument(array $configuration, ?SchemaDocument $schemaDocument = null): string;

    public function tidyDocument(string $document, ?SchemaDocument $schemaDocument = null): string;
}
