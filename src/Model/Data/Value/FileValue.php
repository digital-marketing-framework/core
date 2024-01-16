<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

class FileValue extends Value implements FileValueInterface
{
    final public function __construct(
        protected string $relativePath = '',
        protected string $fileName = '',
        protected string $publicUrl = '',
        protected string $mimeType = '',
    ) {
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
            'relativePath' => $this->getRelativePath(),
            'fileName' => $this->getFileName(),
            'publicUrl' => $this->getPublicUrl(),
            'mimeType' => $this->getMimeType(),
        ];
    }

    public static function unpack(array $packed): FileValueInterface
    {
        return new static(
            relativePath: $packed['relativePath'],
            fileName: $packed['fileName'],
            publicUrl: $packed['publicUrl'],
            mimeType: $packed['mimeType']
        );
    }
}
