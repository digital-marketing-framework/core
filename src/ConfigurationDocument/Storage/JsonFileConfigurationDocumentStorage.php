<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

class JsonFileConfigurationDocumentStorage extends FileConfigurationDocumentStorage
{
    protected function getFileExtension(): string
    {
        return 'json';
    }

    protected function checkFileValidity(string $fileIdentifier): bool
    {
        return parent::checkFileValidity($fileIdentifier)
            && strtolower($this->fileStorage->getFileExtension($fileIdentifier)) === 'json';
    }
}
