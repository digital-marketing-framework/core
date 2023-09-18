<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

class JsonConfigurationDocumentParser extends ConfigurationDocumentParser
{
    public function parseDocument(string $document): array
    {
        $config = json_decode($document, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($config) || $config === []) {
            return [];
        }

        return $config;
    }

    protected function doProduceDocument(array $configuration): string
    {
        return json_encode($configuration, JSON_THROW_ON_ERROR);
    }
}
