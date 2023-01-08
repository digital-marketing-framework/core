<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;

abstract class ConfigurationDocumentParser implements ConfigurationDocumentParserInterface, GlobalConfigurationAwareInterface, LoggerAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use LoggerAwareTrait;

    abstract public function parseDocument(string $document): array;
    abstract public function produceDocument(array $configuration): string;

    public function tidyDocument(string $document): string
    {
        return $this->produceDocument($this->parseDocument($document));
    }
}
