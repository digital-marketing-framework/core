<?php

namespace DigitalMarketingFramework\Core\FileStorage;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValue;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValueInterface;
use DigitalMarketingFramework\Core\Utility\WebServerUtility;

class FileStorage implements FileStorageInterface, LoggerAwareInterface
{
    use AccessProtectionTrait;

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
        $path = $this->getFilePath($fileIdentifier);

        // Writing a file implies the folder it goes in: nobody should have to create the
        // storage folder by hand before the first document can be saved. createFolder() makes
        // only what is missing and protects the folder either way, which is how a folder that
        // was there all along gets its access file.
        $folder = dirname($path);
        $this->createFolder($folder);

        // A file that does not exist yet is never is_writable(), so asking about the file alone
        // would refuse to create anything. For a new file the question is whether its folder
        // takes one.
        $writeable = file_exists($path)
            ? $this->fileIsWriteable($fileIdentifier)
            : is_writable($folder);

        if ($writeable) {
            file_put_contents($path, $fileContent);
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

    // PHP's names for these two are the other way round — its "basename" is the one carrying
    // the extension — which is how they came to be swapped here in the first place.
    public function getFileName(string $fileIdentifier): ?string
    {
        return $this->getFileInfo($fileIdentifier, PATHINFO_BASENAME);
    }

    public function getFileBaseName(string $fileIdentifier): ?string
    {
        return $this->getFileInfo($fileIdentifier, PATHINFO_FILENAME);
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

        return array_filter($list, is_file(...));
    }

    public function folderExists(string $folderIdentifier): bool
    {
        $path = rtrim($this->getFilePath($folderIdentifier), '/');

        return is_dir($path);
    }

    public function folderIsWriteable(string $folderIdentifier): bool
    {
        $path = rtrim($this->getFilePath($folderIdentifier), '/');

        // Walk up to the nearest existing ancestor: a folder that does not exist yet is
        // writeable exactly when something above it will take a new directory.
        while ($path !== '' && !is_dir($path)) {
            $parent = dirname($path);
            if ($parent === $path) {
                return false;
            }

            $path = $parent;
        }

        return $path !== '' && is_writable($path);
    }

    public function createFolder(string $folderIdentifier): void
    {
        if (!$this->folderExists($folderIdentifier)) {
            $path = rtrim($this->getFilePath($folderIdentifier), '/');
            mkdir($path, recursive: true);
        }

        $this->protectFolder($folderIdentifier);
    }

    public function isPubliclyAccessible(string $identifier): bool
    {
        // A plain path says nothing about what a web server serves, so the cautious answer is
        // the one that makes callers warn rather than stay quiet.
        return true;
    }

    public function protectFolder(string $folderIdentifier): void
    {
        if (!WebServerUtility::supportsAccessFile() || !$this->folderExists($folderIdentifier)) {
            return;
        }

        $accessFilePath = rtrim($this->getFilePath($folderIdentifier), '/') . '/' . static::ACCESS_FILE_NAME;
        if (file_exists($accessFilePath)) {
            return;
        }

        // A folder that takes no new file cannot be protected from here. Saying so through a
        // PHP warning on every attempt is not saying it to anyone who can act on it; the
        // storage answers isStorageReady() with false, which is what reaches the backend.
        if (!$this->folderIsWriteable($folderIdentifier)) {
            return;
        }

        file_put_contents($accessFilePath, static::ACCESS_FILE_CONTENTS);
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
