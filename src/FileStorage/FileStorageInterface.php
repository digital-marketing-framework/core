<?php

namespace DigitalMarketingFramework\Core\FileStorage;

interface FileStorageInterface
{
    public function getFileContents(string $fileIdentifier): ?string;

    public function putFileContents(string $fileIdentifier, string $fileContent): void;

    public function deleteFile(string $fileIdentifier): void;

    public function getFileName(string $fileIdentifier): ?string;

    public function getFileBaseName(string $fileIdentifier): ?string;

    public function getFileExtension(string $fileIdentifier): ?string;

    public function fileExists(string $fileIdentifier): bool;

    public function fileIsReadOnly(string $fileIdentifier): bool;

    public function fileIsWriteable(string $fileIdentifier): bool;

    /**
     * @return array<string>
     */
    public function getFilesFromFolder(string $folderIdentifier): array;

    public function folderExists(string $folderIdentifier): bool;

    public function createFolder(string $folderIdentifier): void;
}
