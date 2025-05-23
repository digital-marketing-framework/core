<?php

namespace DigitalMarketingFramework\Core\Resource\Asset;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Resource\ResourceServiceInterface;

class AssetService implements AssetServiceInterface
{
    public const TEMP_PATH_ASSETS = 'assets/vendor-assets';

    protected string $tempBasePath;

    protected string $publicTempBasePath;

    protected string $salt;

    public function __construct(
        protected RegistryInterface $registry,
        ?string $tempBasePath = null,
        ?string $publicTempBasePath = null,
        ?string $salt = null,
    ) {
        $this->tempBasePath = $tempBasePath ?? sys_get_temp_dir();
        $this->publicTempBasePath = $publicTempBasePath ?? '';
        $this->salt = $salt ?? md5('1716664433');
    }

    public function setAssetConfig(array $config): void
    {
        if (isset($config['tempBasePath'])) {
            $this->tempBasePath = $config['tempBasePath'];
        }

        if (isset($config['publicTempBasePath'])) {
            $this->publicTempBasePath = $config['publicTempBasePath'];
        }

        if (isset($config['salt'])) {
            $this->salt = $config['salt'];
        }
    }

    protected function getCacheHash(string $path): string
    {
        return strrev(hash_file('md5', $path));
    }

    protected function updateTargetFolder(string $target): void
    {
        $pathInfo = pathinfo($target);
        $folder = $pathInfo['dirname'];
        if (!is_dir($folder)) {
            if (file_exists($folder)) {
                throw new DigitalMarketingFrameworkException(sprintf('Asset target folder "%s" seems to be a file.', $folder));
            }

            mkdir($folder, recursive: true);
        }
    }

    protected function copyFile(string $source, string $target): void
    {
        $copy = false;
        if (file_exists($target)) {
            if ($this->getCacheHash($source) !== $this->getCacheHash($target)) {
                unlink($target);
                $copy = true;
            }
        } else {
            $this->updateTargetFolder($target);
            $copy = true;
        }

        if ($copy) {
            copy($source, $target);
        }
    }

    protected function getPublicTempPath(): string
    {
        $basePath = trim($this->publicTempBasePath, '/');

        if ($basePath !== '') {
            return $basePath . '/' . static::TEMP_PATH_ASSETS;
        }

        return static::TEMP_PATH_ASSETS;
    }

    public function makeAssetPublic(string $identifier): ?string
    {
        $resourceService = $this->registry->getResourceService($identifier);

        if (!$resourceService instanceof ResourceServiceInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('No resource service found for identifier "%s".', $identifier));
        }

        if (!$resourceService->validateResourceIdentifier($identifier)) {
            throw new DigitalMarketingFrameworkException(sprintf('Resource "%s" not valid.', $identifier));
        }

        if (!$resourceService->isAssetResource($identifier)) {
            throw new DigitalMarketingFrameworkException(sprintf('Resource "%s" is not a public asset.', $identifier));
        }

        $source = $resourceService->getResourcePath($identifier);

        $relativePath1 = trim($resourceService->getResourceRootPath($identifier), '/');
        $relativePath2 = trim(substr((string)$source, strlen($relativePath1) + 1), '/');
        $relativeTarget = strrev(md5($relativePath1 . '|' . $this->salt)) . '/' . $relativePath2;

        $target = $this->tempBasePath . '/' . static::TEMP_PATH_ASSETS . '/' . $relativeTarget;

        $this->copyFile($source, $target);

        $publicTarget = $this->getPublicTempPath() . '/' . $relativeTarget;
        $cacheHash = $this->getCacheHash($source);

        if ($cacheHash !== '') {
            return $publicTarget . '?' . $cacheHash;
        }

        return $publicTarget;
    }
}
