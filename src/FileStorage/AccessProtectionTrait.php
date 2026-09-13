<?php

namespace DigitalMarketingFramework\Core\FileStorage;

use DigitalMarketingFramework\Core\Utility\WebServerUtility;

/**
 * The one definition of what "protected" means, shared by every file storage.
 */
trait AccessProtectionTrait
{
    abstract public function isPubliclyAccessible(string $identifier): bool;

    abstract public function fileExists(string $fileIdentifier): bool;

    abstract public function folderExists(string $folderIdentifier): bool;

    public function folderIsProtected(string $folderIdentifier): bool
    {
        // A storage nothing serves needs no file to be safe, and is the outcome to aim for.
        if (!$this->isPubliclyAccessible($folderIdentifier)) {
            return true;
        }

        // A public folder on a server that ignores access files cannot be protected from here at
        // all — not by a file lying in it, and not by staying empty either, since the first
        // thing written into it will be fetchable. Asked before the folder exists, because that
        // is when there is still something the answer can change.
        if (!WebServerUtility::supportsAccessFile()) {
            return false;
        }

        // A folder that is not there has nothing to fetch, and gets its access file as it is
        // created, which happens with the first file written into it.
        if (!$this->folderExists($folderIdentifier)) {
            return true;
        }

        return $this->fileExists(rtrim($folderIdentifier, '/') . '/' . static::ACCESS_FILE_NAME);
    }
}
