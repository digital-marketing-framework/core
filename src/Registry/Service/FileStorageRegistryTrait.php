<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\FileStorage\FileStorage;
use DigitalMarketingFramework\Core\FileStorage\FileStorageInterface;

trait FileStorageRegistryTrait
{
    protected FileStorageInterface $fileStorage;

    public function getFileStorage(): FileStorageInterface
    {
        if (!isset($this->fileStorage)) {
            $this->fileStorage = $this->createObject(FileStorage::class);
        }
        return $this->fileStorage;
    }

    public function setFileStorage(FileStorageInterface $fileStorage): void
    {
        $this->fileStorage = $fileStorage;
    }
}
