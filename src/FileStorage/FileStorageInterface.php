<?php

namespace DigitalMarketingFramework\Core\FileStorage;

use DigitalMarketingFramework\Core\Model\Data\Value\FileValueInterface;

interface FileStorageInterface
{
    public const ACCESS_FILE_NAME = '.htaccess';

    /**
     * Apache only. Nothing here helps on nginx, which is why this is a fallback for a storage
     * that should not have been public rather than a way to make a public one safe.
     */
    public const ACCESS_FILE_CONTENTS = <<<'HTACCESS'
        # Added by Anyrel. The folder it sits in holds configuration documents and field
        # definitions, which have no reason to be reachable over the web.

        # Apache < 2.3
        <IfModule !mod_authz_core.c>
            Order allow,deny
            Deny from all
            Satisfy All
        </IfModule>

        # Apache >= 2.3
        <IfModule mod_authz_core.c>
            Require all denied
        </IfModule>
        HTACCESS;

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

    /**
     * Whether a folder could be written to, whether or not it exists yet.
     *
     * Distinct from folderExists(): a folder that is missing but creatable is a usable place to
     * store something, and asking about existence would report an empty storage as broken.
     */
    public function folderIsWriteable(string $folderIdentifier): bool;

    /**
     * Whether files in this folder can be fetched over the web.
     *
     * Anyrel keeps documents in whatever storage an integrator configured, and a public one is
     * the wrong place for them even when they hold nothing secret. Callers use this to say so
     * rather than to prevent it.
     */
    public function isPubliclyAccessible(string $identifier): bool;

    /**
     * Whether a folder's contents are in fact unreachable over the web.
     *
     * True when the storage is not public at all, or when this server reads access files and one
     * is in place. False otherwise, including the case where nothing can be done from here.
     */
    public function folderIsProtected(string $folderIdentifier): bool;

    /**
     * Best-effort attempt to make a folder's contents unreachable over the web.
     *
     * Does nothing where it would be meaningless — a storage that is not public already, or one
     * no local web server serves. Implementations must be cheap and repeatable: callers invoke
     * this whenever they know the folder exists rather than tracking whether it was done.
     */
    public function protectFolder(string $folderIdentifier): void;

    public function getTempPath(): string;

    public function writeTempFile(string $filePrefix, string $fileContent, string $fileSuffix): string|bool;
}
