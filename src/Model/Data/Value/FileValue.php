<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

use DigitalMarketingFramework\Core\Model\File\FileInterface;

class FileValue extends Value implements FileValueInterface
{
    protected string $fileName = '';

    protected string $publicUrl = '';

    protected string $relativePath = '';

    protected string $mimeType = '';

    final public function __construct(?FileInterface $file = null)
    {
        if ($file instanceof FileInterface) {
            $this->fileName = $file->getName();
            $this->publicUrl = $file->getPublicUrl();
            $this->relativePath = $file->getRelativePath();
            $this->mimeType = $file->getMimeType();
        }
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setPublicUrl(string $publicUrl): void
    {
        $this->publicUrl = $publicUrl;
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    public function setRelativePath(string $relativePath): void
    {
        $this->relativePath = $relativePath;
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function __toString(): string
    {
        return $this->getPublicUrl();
    }

    public function pack(): array
    {
        return [
            'fileName' => $this->getFileName(),
            'publicUrl' => $this->getPublicUrl(),
            'relativePath' => $this->getRelativePath(),
            'mimeType' => $this->getMimeType(),
        ];
    }

    public static function unpack(array $packed): FileValueInterface
    {
        $field = new static();
        $field->setFileName($packed['fileName']);
        $field->setPublicUrl($packed['publicUrl']);
        $field->setRelativePath($packed['relativePath']);
        $field->setMimeType($packed['mimeType']);

        return $field;
    }
}
