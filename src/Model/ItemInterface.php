<?php

namespace DigitalMarketingFramework\Core\Model;

interface ItemInterface
{
    public function getId(): int|string|null;

    public function setId(int|string $id): void;

    public function getLabel(): string;
}
