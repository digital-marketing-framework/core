<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

interface ConfigurationDocumentParserAwareInterface
{
    public function setConfigurationDocumentParser(ConfigurationDocumentParserInterface $configurationDocumentParser): void;
}
