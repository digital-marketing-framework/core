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
    public function getId();

    /**
     * @param IdType $id
     */
    public function setId($id): void;

    public function getLabel(): string;
}
