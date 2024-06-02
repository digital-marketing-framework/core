<?php

namespace DigitalMarketingFramework\Core\Resource;

interface ResourceServiceInterface
{
    public function getIdentifierPrefix(): string;

    public function getResourcePath(string $identifier): ?string;

    /**
     * Evaluates whether a given identifier matches to pattern of the resource service.
     * Does not check the resource behind the identifier itself.
     */
    public function resourceIdentifierMatch(string $identifier): bool;

    /**
     * Checks the validity of the resource behind a given identifier.
     */
    public function validateResourceIdentifier(string $identifier): bool;

    /**
     * Checks if the resource behind the given identifier is within a given resource sub folder.
     */
    public function isResourceInFolder(string $identifier, string $folder): bool;

    public function isAssetResource(string $identifier): bool;

    public function isPublicResource(string $identifier): bool;

    public function getFileIdentifierInResourceFolder(string $folderIdentifier, string $file): ?string;

    /**
     * @return array<string>|false
     */
    public function getFilesInResourceFolder(string $folderIdentifier): array|false;

    /**
     * @return array<string>|false
     */
    public function getFileIdentifiersInResourceFolder(string $folderIdentifier): array|false;

    public function getResourceRootIdentifier(string $identifier): ?string;

    public function getResourceRootPath(string $identifier, ?string $subFolder = null): string;

    public function resourceExists(string $identifier): bool;

    public function readOnly(string $identifier): bool;

    public function getResourceContent(string $identifier): ?string;

    public function setResourceContent(string $identifier, string $content): bool;
}
