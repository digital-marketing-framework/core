<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

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

    protected function doProduceDocument(array $configuration): string
    {
        return json_encode($configuration);
    }
}
