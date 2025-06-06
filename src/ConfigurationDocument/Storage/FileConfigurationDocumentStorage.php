<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\ConfigurationDocument\Exception\ConfigurationDocumentNotFoundException;
use DigitalMarketingFramework\Core\FileStorage\FileStorageAwareInterface;
use DigitalMarketingFramework\Core\FileStorage\FileStorageAwareTrait;

abstract class FileConfigurationDocumentStorage extends ConfigurationDocumentStorage implements FileStorageAwareInterface
{
    use FileStorageAwareTrait;

    /** @var array<string,string> */
    protected array $cache = [];

    abstract protected function getFileExtension(): string;

    public function getDocument(string $documentIdentifier, bool $metaDataOnly = false): string
    {
        if (isset($this->cache[$documentIdentifier])) {
            return $this->cache[$documentIdentifier];
        }

        if (!$this->fileStorage->fileExists($documentIdentifier)) {
            throw new ConfigurationDocumentNotFoundException(sprintf('Configuration document file not found: %s', $documentIdentifier));
        }

        return $this->fileStorage->getFileContents($documentIdentifier);
    }

    public function setDocument(string $documentIdentifier, string $document): void
    {
        $this->cache[$documentIdentifier] = $document;
        $this->fileStorage->putFileContents($documentIdentifier, $document);
    }

    public function deleteDocument(string $documentIdentifier): void
    {
        unset($this->cache[$documentIdentifier]);
        $this->fileStorage->deleteFile($documentIdentifier);
    }

    public function isReadOnly(string $documentIdentifier): bool
    {
        return $this->fileStorage->fileIsReadOnly($documentIdentifier);
    }

    protected function checkFileValidity(string $fileIdentifier): bool
    {
        $baseFileName = $this->fileStorage->getFileBaseName($fileIdentifier);

        return (bool)preg_match('/.config$/', strtolower((string)$baseFileName));
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

    protected function buildFileBaseName(string $documentBaseName): string
    {
        $baseName = $documentBaseName;
        $baseName = preg_replace_callback('/[A-Z]+/', static fn (array $matches): string => '-' . strtolower($matches[0]), $baseName);
        $baseName = preg_replace('/[^a-zA-Z0-9]+/', '-', (string)$baseName);
        $baseName = preg_replace('/^[^a-zA-Z0-9]+/', '', (string)$baseName);

        return preg_replace('/[^a-zA-Z0-9]+$/', '', (string)$baseName);
    }

    public function getDocumentIdentifierFromBaseName(string $baseName, bool $newFile = true): string
    {
        $folder = $this->getStorageFolderIdentifier();
        $baseName = $this->buildFileBaseName($baseName);
        $extension = $this->getFileExtension();
        $count = 0;
        do {
            $fileIdentifier = sprintf('%s/%s%s.config.%s', $folder, $baseName, $count > 0 ? '-' . $count : '', $extension);
            ++$count;
        } while (!$newFile || $this->fileStorage->fileExists($fileIdentifier));

        return $fileIdentifier;
    }

    public function getShortIdentifier(string $documentIdentifier): string
    {
        $baseName = $this->fileStorage->getFileBaseName($documentIdentifier);
        $baseNameParts = explode('.', (string)$baseName);
        array_pop($baseNameParts);

        return implode('.', $baseNameParts);
    }

    protected function getStorageFolderIdentifier(): string
    {
        return rtrim((string)$this->getStorageConfiguration('folder', ''), '/');
    }

    public function initializeConfigurationDocumentStorage(): void
    {
        $folderIdentifier = $this->getStorageFolderIdentifier();
        if ($folderIdentifier !== '' && !$this->fileStorage->folderExists($folderIdentifier)) {
            $this->fileStorage->createFolder($folderIdentifier);
        }
    }
}
