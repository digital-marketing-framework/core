<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Parser;

trait ConfigurationDocumentParserAwareTrait
{
    protected ConfigurationDocumentParserInterface $configurationDocumentParser;

    public function setConfigurationDocumentParser(ConfigurationDocumentParserInterface $configurationDocumentParser): void
    {
        $this->configurationDocumentParser = $configurationDocumentParser;
    }
}
