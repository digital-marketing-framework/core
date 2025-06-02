<?php

namespace DigitalMarketingFramework\Core\Model;

/**
 * @template IdType of int|string
 */
interface ItemInterface
{
    /**
     * @return ?IdType
     */
    public function getId(): int|string|null;

    public function getLabel(): string;
}
