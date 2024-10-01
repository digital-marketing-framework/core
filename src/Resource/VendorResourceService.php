<?php

namespace DigitalMarketingFramework\Core\Resource;

class VendorResourceService extends ResourceService implements VendorResourceServiceInterface
{
    public const IDENTIFIER_PREFIX = 'PKG';

    public function __construct(
        protected string $vendorPath = 'vendor',
    ) {
    }

    public function setVendorPath(string $vendorPath): void
    {
        $this->vendorPath = $vendorPath;
    }

    public function getVendorPath(): string
    {
        return $this->vendorPath;
    }

    public function isAssetResource(string $identifier): bool
    {
        return $this->isResourceInFolder($identifier, 'assets');
    }

    public function isPublicResource(string $identifier): bool
    {
        return false;
    }

    public function getResourceRootIdentifier(string $identifier): ?string
    {
        $fileInfo = $this->getVendorResourceFileInfo($identifier);

        if ($fileInfo === false) {
            return null;
        }

        return static::IDENTIFIER_PREFIX . ':' . $fileInfo['package'] . '/res';
    }

    public function getIdentifierPrefix(): string
    {
        return static::IDENTIFIER_PREFIX;
    }

    public function getResourcePath(string $identifier): ?string
    {
        $fileInfo = $this->getVendorResourceFileInfo($identifier);

        if ($fileInfo === false) {
            return null;
        }

        return $this->getVendorPath() . '/' . $fileInfo['package'] . '/' . $fileInfo['path'];
    }

    public function resourceIdentifierMatch(string $identifier): bool
    {
        return $this->getVendorResourceFileInfo($identifier) !== false;
    }

    /**
     * @return array{package:string,path:string}|false
     */
    public function getVendorResourceFileInfo(string $identifier): array|false
    {
        $matches = [];
        if (preg_match('/^' . static::IDENTIFIER_PREFIX . ':([-_a-zA-Z0-9]+\\/[-_a-zA-Z0-9]+)\\/(res(\\/.+)?)$/', $identifier, $matches)) {
            return [
                'package' => $matches[1],
                'path' => $matches[2],
            ];
        }

        return false;
    }
}
