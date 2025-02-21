<?php

namespace DigitalMarketingFramework\Core\Model\Alert;

interface AlertInterface
{
    public const TYPE_INFO = 0;

    public const TYPE_WARNING = 1;

    public const TYPE_ERROR = 2;

    public function getSource(): string;

    public function setSource(string $source): void;

    public function getContent(): string;

    public function setContent(string $content): void;

    public function getTitle(): ?string;

    public function setTitle(?string $title): void;

    public function getType(): int;

    public function setType(int $type): void;
}
