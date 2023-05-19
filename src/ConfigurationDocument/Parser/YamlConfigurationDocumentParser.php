<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class YamlConfigurationDocumentParser extends ConfigurationDocumentParser
{
    public function parseDocument(string $document): array
    {
        try {
            $config = Yaml::parse($document);
        } catch (ParseException $e) {
            throw new DigitalMarketingFrameworkException($e->getMessage(), $e->getCode(), $e);
        }
        if ($config === null) {
            // and empty document is allowed and translates to an empty configuration
            $config = [];
        }
        if (!is_array($config) || empty($config)) {
            return [];
        }
        return $config;
    }

    public function produceDocument(array $configuration): string
    {
        try {
            return Yaml::dump($configuration);
        } catch (ParseException $e) {
            throw new DigitalMarketingFrameworkException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
