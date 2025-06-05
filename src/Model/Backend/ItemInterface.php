<?php

namespace DigitalMarketingFramework\Core\Model\Backend;

interface ItemInterface
{
    public function getId(): int|string|null;

    public function getLabel(): string;
}
