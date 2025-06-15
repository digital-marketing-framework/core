<?php

namespace DigitalMarketingFramework\Core\Model;

abstract class Item implements ItemInterface
{
    protected int|string|null $id;

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function setId(int|string|null $id): void
    {
        $this->id = $id;
    }
}
