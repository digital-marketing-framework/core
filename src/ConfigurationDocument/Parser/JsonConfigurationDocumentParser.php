<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

class JsonConfigurationDocumentParser extends ConfigurationDocumentParser
{
    public function parseDocument(string $document): array
    {
        $config = json_decode($document, true);

        if (!is_array($config) || empty($config)) {
            return [];
        }

        return $config;
    }

    public function produceDocument(array $configuration): string
    {
        return json_encode($configuration);
    }
}
