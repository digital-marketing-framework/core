<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

interface FileValueInterface extends ValueInterface
{
    public function setFileName(string $fileName);
    public function getFileName(): string;
    public function setPublicUrl(string $publicUrl);
    public function getPublicUrl(): string;
    public function setRelativePath(string $relativePath);
    public function getRelativePath(): string;
    public function setMimeType(string $mimeType);
    public function getMimeType(): string;
    public function __toString(): string;
    public function pack(): array;
    public static function unpack(array $packed): FileValueInterface;
}
