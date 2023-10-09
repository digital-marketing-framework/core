<?php

namespace DigitalMarketingFramework\Core\FileStorage;

use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;

class FileStorage implements FileStorageInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected function getFilePath(string $fileIdentifier): string
    {
        return $fileIdentifier;
    }

    public function getFileContents(string $fileIdentifier): ?string
    {
        if (!$this->fileExists($fileIdentifier)) {
            return null;
        }

        return file_get_contents($this->getFilePath($fileIdentifier));
    }

    public function putFileContents(string $fileIdentifier, string $fileContent): void
    {
        if ($this->fileIsWriteable($fileIdentifier)) {
            file_put_contents($this->getFilePath($fileIdentifier), $fileContent);
        } else {
            $this->logger->warning(sprintf('File %s does not seem to be writeable.', $fileIdentifier));
        }
    }

    public function deleteFile(string $fileIdentifier): void
    {
        unlink($this->getFilePath($fileIdentifier));
    }

    protected function getFileInfo(string $fileIdentifier, int $flag): ?string
    {
        if ($this->fileExists($fileIdentifier)) {
            return pathinfo($this->getFilePath($fileIdentifier), $flag);
        }

        $this->logger->warning(sprintf('File %s does not seem to exist.', $fileIdentifier));

        return null;
    }

    public function getFileName(string $fileIdentifier): ?string
    {
        return $this->getFileInfo($fileIdentifier, PATHINFO_FILENAME);
    }

    public function getFileBaseName(string $fileIdentifier): ?string
    {
        return $this->getFileInfo($fileIdentifier, PATHINFO_BASENAME);
    }

    public function getFileExtension(string $fileIdentifier): ?string
    {
        return $this->getFileInfo($fileIdentifier, PATHINFO_EXTENSION);
    }

    public function fileExists(string $fileIdentifier): bool
    {
        return file_exists($this->getFilePath($fileIdentifier));
    }

    public function fileIsReadOnly(string $fileIdentifier): bool
    {
        if (preg_match('/^[A-Z]{2,}:/', $fileIdentifier)) {
            // identifiers like SYS:xxxxxxx are internal and those are always readonly
            // we expect at least two letters though, so that we do not catch windows paths like C:\foobar
            return true;
        }

        return $this->fileExists($fileIdentifier) && !$this->fileIsWriteable($fileIdentifier);
    }

    public function fileIsWriteable(string $fileIdentifier): bool
    {
        return is_writable($this->getFilePath($fileIdentifier));
    }

    public function getFilesFromFolder(string $folderIdentifier): array
    {
        if (!$this->folderExists($folderIdentifier)) {
            return [];
        }

        $path = rtrim($this->getFilePath($folderIdentifier), '/');
        $list = scandir($path);
        $list = array_map(static function (string $file) use ($path) {
            return $path . '/' . $file;
        }, $list);

        return array_filter($list, static function (string $file) {
            return is_file($file);
        });
    }

    public function folderExists(string $folderIdentifier): bool
    {
        $path = rtrim($this->getFilePath($folderIdentifier), '/');

        return is_dir($path);
    }

    public function createFolder(string $folderIdentifier): void
    {
        if (!$this->folderExists($folderIdentifier)) {
            $path = rtrim($this->getFilePath($folderIdentifier), '/');
            mkdir($path, recursive: true);
        }
    }
}
