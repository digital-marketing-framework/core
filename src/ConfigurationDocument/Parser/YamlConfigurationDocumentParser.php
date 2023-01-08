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
        if (!is_array($config) || empty($config)) {
            throw new DigitalMarketingFrameworkException('Configuration document seems to be empty');
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
