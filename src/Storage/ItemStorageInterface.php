<?php

namespace DigitalMarketingFramework\Core\Storage;

use DigitalMarketingFramework\Core\Model\ItemInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;

/**
 * @template ItemClass of ItemInterface
 * @template IdType of int|string
 * @template ItemData of array<string,mixed>
 * @template Filters of array<string,mixed>
 */
interface ItemStorageInterface
{
    /**
     * @param ?ItemData $data
     *
     * @return ItemClass
     */
    public function create(?array $data = null);

    /**
     * @param ItemClass $item
     */
    public function add($item): void;

    /**
     * @param ItemClass $item
     */
    public function remove($item): void;

    /**
     * @param ItemClass $item
     */
    public function update($item): void;

    /**
     * @param IdType $id
     *
     * @return ?ItemClass
     */
    public function fetchById($id);

    public function countAll(): int;

    /**
     * @param ?array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     *
     * @return array<ItemClass>
     */
    public function fetchAll(?array $navigation = null): array;

    /**
     * @param Filters $filters
     */
    public function countFiltered(array $filters): int;

    /**
     * @param Filters $filters
     * @param ?array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     *
     * @return array<ItemClass>
     */
    public function fetchFiltered(array $filters, ?array $navigation = null): array;

    public static function getSchema(): ContainerSchema;
}
