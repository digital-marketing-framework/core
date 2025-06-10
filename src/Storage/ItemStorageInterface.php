<?php

namespace DigitalMarketingFramework\Core\Storage;

use DigitalMarketingFramework\Core\Model\ItemInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;

/**
 * @template ItemClass of ItemInterface
 * @template IdType of int|string
 */
interface ItemStorageInterface
{
    /**
     * @param ?array<string,mixed> $data
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
     * @param array<string,mixed> $filters
     */
    public function countFiltered(array $filters): int;

    /**
     * @param array<string,mixed> $filters
     * @param ?array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     *
     * @return array<ItemClass>
     */
    public function fetchFiltered(array $filters, ?array $navigation = null): array;

    /**
     * @param array<string,mixed> $filters
     *
     * @return ?ItemClass
     */
    public function fetchOneFiltered(array $filters);

    /**
     * @param array<IdType> $ids
     */
    public function fetchByIdList(array $ids): array;

    public static function getSchema(): ContainerSchema;
}
