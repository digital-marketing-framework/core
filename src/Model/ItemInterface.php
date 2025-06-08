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

    /**
     * @param IdType $id
     */
    public function setId($id): void;

    public function getLabel(): string;
}
