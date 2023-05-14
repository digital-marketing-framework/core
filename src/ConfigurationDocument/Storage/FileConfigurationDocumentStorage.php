<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\ConfigurationDocument\Exception\ConfigurationDocumentNotFoundException;
use DigitalMarketingFramework\Core\FileStorage\FileStorageAwareInterface;
use DigitalMarketingFramework\Core\FileStorage\FileStorageAwareTrait;

abstract class FileConfigurationDocumentStorage extends ConfigurationDocumentStorage implements FileStorageAwareInterface
{
    use FileStorageAwareTrait;

    protected array $storageConfiguration;

    public function getDocument(string $documentIdentifier): string
    {
        if (!$this->fileStorage->fileExists($documentIdentifier)) {
            throw new ConfigurationDocumentNotFoundException(sprintf('Configuration document file not found: %s', $documentIdentifier));
        }

        return $this->fileStorage->getFileContents($documentIdentifier);
    }

    public function setDocument(string $documentIdentifier, string $document): void
    {
        $this->fileStorage->putFileContents($documentIdentifier, $document);
    }

    public function isReadOnly(string $documentIdentifier): bool
    {
        return $this->fileStorage->fileIsReadOnly($documentIdentifier);
    }

    protected function checkFileValidity(string $fileIdentifier): bool
    {
        $baseFileName = $this->fileStorage->getFileBaseName($fileIdentifier);
        return preg_match('/.config$/', strtolower($baseFileName));
    }

    public function getDocumentIdentifiers(): array
    {
        $folderIdentifier = $this->getStorageConfiguration('folder');
        if (empty($folderIdentifier)) {
            return [];
        }
        $fileIdentifiers = $this->fileStorage->getFilesFromFolder($folderIdentifier);
        $result = [];
        foreach ($fileIdentifiers as $fileIdentifier) {
            if ($this->checkFileValidity($fileIdentifier)) {
                $result[] = $fileIdentifier;
            }
        }
        return $result;
    }

    protected function getStorageFolderIdentifier(): string
    {
        return rtrim($this->getStorageConfiguration('folder', ''), '/');
    }

    public function initalizeConfigurationDocumentStorage(): void
    {
        $folderIdentifier = $this->getStorageFolderIdentifier();
        if ($folderIdentifier !== '' && !$this->fileStorage->folderExists($folderIdentifier)) {
            $this->fileStorage->createFolder($folderIdentifier);
        }
    }
}
