<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

interface FileValueInterface extends ValueInterface
{
    public function setFileName(string $fileName): void;

    public function getFileName(): string;

    public function setPublicUrl(string $publicUrl): void;

    public function getPublicUrl(): string;

    public function setRelativePath(string $relativePath): void;

    public function getRelativePath(): string;

    public function setMimeType(string $mimeType): void;

    public function getMimeType(): string;

    public function __toString(): string;

    public function pack(): array;

    public static function unpack(array $packed): FileValueInterface;
}
