<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\FileStorage\FileStorageInterface;

interface FileStorageRegistryInterface
{
    public function getFileStorage(): FileStorageInterface;

    public function setFileStorage(FileStorageInterface $fileStorage): void;
}
