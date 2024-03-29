<?php

namespace DigitalMarketingFramework\Core\FileStorage;

use DigitalMarketingFramework\Core\Model\Data\Value\FileValueInterface;

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

    public function getPublicUrl(string $fileIdentifier): string;

    public function getMimeType(string $fileIdentifier): string;

    public function getFileValue(string $fileIdentifier): ?FileValueInterface;

    public function copyFileToFolder(string $fileIdentifier, string $folderIdentifier): string;

    /**
     * @return array<string>
     */
    public function getFilesFromFolder(string $folderIdentifier): array;

    public function folderExists(string $folderIdentifier): bool;

    public function createFolder(string $folderIdentifier): void;

    public function getTempPath(): string;

    public function writeTempFile(string $filePrefix, string $fileContent, string $fileSuffix): string|bool;
}
