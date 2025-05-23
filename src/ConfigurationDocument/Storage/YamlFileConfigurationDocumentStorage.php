<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

class YamlFileConfigurationDocumentStorage extends FileConfigurationDocumentStorage
{
    protected function getFileExtension(): string
    {
        return 'yaml';
    }

    protected function checkFileValidity(string $fileIdentifier): bool
    {
        return parent::checkFileValidity($fileIdentifier)
            && in_array(
                strtolower((string)$this->fileStorage->getFileExtension($fileIdentifier)),
                ['yml', 'yaml'],
                true
            );
    }
}
