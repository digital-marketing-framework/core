<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

interface ConfigurationDocumentParserInterface
{
    public function parseDocument(string $document): array;
    public function produceDocument(array $configuration, ?SchemaDocument $schemaDocument = null): string;
    public function tidyDocument(string $document, ?SchemaDocument $schemaDocument = null): string;
}
