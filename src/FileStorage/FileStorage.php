<?php

namespace DigitalMarketingFramework\Core\FileStorage;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValue;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValueInterface;

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

        $contents = file_get_contents($this->getFilePath($fileIdentifier));

        return $contents === false ? null : $contents;
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
            /** @var string */
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

    public function copyFileToFolder(string $fileIdentifier, string $folderIdentifier): string
    {
        if (!$this->fileExists($fileIdentifier)) {
            throw new DigitalMarketingFrameworkException(sprintf('File "%s" not found', $fileIdentifier));
        }

        if (!$this->folderExists($folderIdentifier)) {
            throw new DigitalMarketingFrameworkException(sprintf('Folder "%s" not found', $folderIdentifier));
        }

        $name = $this->getFileName($fileIdentifier);
        $targetFileIdentifier = rtrim($folderIdentifier, '/') . '/' . $name;

        $contents = $this->getFileContents($fileIdentifier);
        $this->putFileContents($targetFileIdentifier, $contents);

        return $targetFileIdentifier;
    }

    public function getFilesFromFolder(string $folderIdentifier): array
    {
        if (!$this->folderExists($folderIdentifier)) {
            return [];
        }

        $path = rtrim($this->getFilePath($folderIdentifier), '/');
        $list = scandir($path);
        if ($list === false) {
            $list = [];
        }

        $list = array_map(static fn (string $file): string => $path . '/' . $file, $list);

        return array_filter($list, static fn (string $file): bool => is_file($file));
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

    public function getPublicUrl(string $fileIdentifier): string
    {
        return '';
    }

    public function getMimeType(string $fileIdentifier): string
    {
        $mimeType = mime_content_type($this->getFilePath($fileIdentifier));
        if ($mimeType === false) {
            return '';
        }

        return $mimeType;
    }

    public function getFileValue(string $fileIdentifier): ?FileValueInterface
    {
        if (!$this->fileExists($fileIdentifier)) {
            return null;
        }

        return new FileValue(
            $fileIdentifier,
            $this->getFileName($fileIdentifier) ?? '',
            $this->getPublicUrl($fileIdentifier),
            $this->getMimeType($fileIdentifier)
        );
    }

    public function getTempPath(): string
    {
        return sys_get_temp_dir();
    }

    public function writeTempFile(string $filePrefix = '', string $fileContent = '', string $fileSuffix = ''): string|false
    {
        $result = false;
        $temporaryPath = $this->getTempPath();
        if ($fileSuffix === '') {
            $path = (string)tempnam($temporaryPath, $filePrefix);
            $filePath = $temporaryPath . '/' . basename($path);
        } else {
            do {
                $filePath = $temporaryPath . $filePrefix . random_int(1, PHP_INT_MAX) . $fileSuffix;
            } while (file_exists($filePath));

            touch($filePath);
            clearstatcache(false, $filePath);
        }

        if (is_writable($filePath)) {
            $result = file_put_contents($filePath, $fileContent);
        } else {
            $this->logger->warning(sprintf('File %s does not seem to be writeable.', $filePath));
        }

        return $result ? $filePath : false;
    }
}
