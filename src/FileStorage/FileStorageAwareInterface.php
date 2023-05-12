<?php

namespace DigitalMarketingFramework\Core\FileStorage;

interface FileStorageAwareInterface
{
    public function setFileStorage(FileStorageInterface $fileStorage): void;
}
