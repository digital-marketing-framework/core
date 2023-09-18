<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

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

        if (!is_array($config) || $config === []) {
            return [];
        }

        return $config;
    }

    protected function doProduceDocument(array $configuration): string
    {
        try {
            return Yaml::dump($configuration, inline: 25, flags: Yaml::DUMP_OBJECT_AS_MAP);
        } catch (ParseException $e) {
            throw new DigitalMarketingFrameworkException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
