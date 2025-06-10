<?php

namespace DigitalMarketingFramework\Core\Model;

/**
 * @template IdType of sting|int
 *
 * @implements ItemInterface<IdType>
 */
abstract class Item implements ItemInterface
{
    /** @var ?IdType */
    protected $id = null;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }
}
