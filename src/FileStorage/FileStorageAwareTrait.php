<?php

namespace DigitalMarketingFramework\Core\FileStorage;

trait FileStorageAwareTrait
{
    protected FileStorageInterface $fileStorage;

    public function setFileStorage(FileStorageInterface $fileStorage): void
    {
        $this->fileStorage = $fileStorage;
    }
}
