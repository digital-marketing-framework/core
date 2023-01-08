<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

interface ConfigurationDocumentParserInterface
{
    public function parseDocument(string $document): array;
    public function produceDocument(array $configuration): string;
    public function tidyDocument(string $document): string;
}
