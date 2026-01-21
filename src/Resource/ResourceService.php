<?php

namespace DigitalMarketingFramework\Core\Resource;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;

abstract class ResourceService implements ResourceServiceInterface, GlobalConfigurationAwareInterface
{
    use GlobalConfigurationAwareTrait;

    public const IGNORE_FILES = [
        '.',
        '..',
        '.gitignore',
    ];

    public function getResourceRootPath(string $identifier, ?string $subFolder = null): string
    {
        $path = $this->getResourcePath($this->getResourceRootIdentifier($identifier));

        if ($subFolder !== null) {
            $path .= '/' . $subFolder;
        }

        return $path;
    }

    public function validateResourceIdentifier(string $identifier): bool
    {
        if (!$this->resourceIdentifierMatch($identifier)) {
            return false;
        }

        $path = $this->getResourcePath($identifier);

        if (!file_exists($path)) {
            return false;
        }

        $rootPath = $this->getResourceRootPath($identifier);

        return str_starts_with(realpath($path), realpath($rootPath));
    }

    public function isResourceInFolder(string $identifier, string $folder): bool
    {
        if (!$this->resourceIdentifierMatch($identifier)) {
            return false;
        }

        $path = $this->getResourcePath($identifier);

        if (!file_exists($path)) {
            return false;
        }

        $parentPath = $this->getResourceRootPath($identifier, $folder);

        return str_starts_with(realpath($path), realpath($parentPath));
    }

    public function getFileIdentifierInResourceFolder(string $folderIdentifier, string $file): ?string
    {
        return $folderIdentifier . '/' . $file;
    }

    public function getFilesInResourceFolder(string $folderIdentifier): array|false
    {
        if (!$this->resourceIdentifierMatch($folderIdentifier)) {
            return false;
        }

        $path = $this->getResourcePath($folderIdentifier);

        if ($path === null) {
            return false;
        }

        $result = [];
        if (is_dir($path)) {
            $files = scandir($path);

            if ($files === false) {
                $files = [];
            }

            foreach ($files as $file) {
                if (in_array($file, self::IGNORE_FILES, true)) {
                    continue;
                }

                $result[] = $file;
            }
        }

        return $result;
    }

    public function getFileIdentifiersInResourceFolder(string $folderIdentifier): array|false
    {
        $files = $this->getFilesInResourceFolder($folderIdentifier);

        if ($files === false) {
            return false;
        }

        $result = [];
        foreach ($files as $file) {
            $result[] = $this->getFileIdentifierInResourceFolder($folderIdentifier, $file);
        }

        return $result;
    }

    public function readOnly(string $identifier): bool
    {
        return !(bool)($this->globalConfiguration->get('core', [])['configurationStorage']['allowSaveToExtensionPaths'] ?? false);
    }

    public function resourceExists(string $identifier): bool
    {
        $path = $this->getResourcePath($identifier);

        if ($path === null) {
            return false;
        }

        return file_exists($path);
    }

    public function getResourceContent(string $identifier): ?string
    {
        $path = $this->getResourcePath($identifier);

        if ($path === null) {
            return null;
        }

        $content = file_get_contents($path);

        if ($content === false) {
            return null;
        }

        return $content;
    }

    public function setResourceContent(string $identifier, string $content): bool
    {
        $path = $this->getResourcePath($identifier);

        if ($path === null) {
            return false;
        }

        file_put_contents($path, $content);

        return true;
    }
}
