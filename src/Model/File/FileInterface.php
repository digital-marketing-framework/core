<?php

namespace DigitalMarketingFramework\Core\Model\File;

interface FileInterface
{
    public function getName(): string;

    public function getPublicUrl(): string;

    public function getRelativePath(): string;

    public function getMimeType(): string;
}
