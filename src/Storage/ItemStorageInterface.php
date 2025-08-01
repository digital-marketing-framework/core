<?php

namespace DigitalMarketingFramework\Core\Storage;

use DigitalMarketingFramework\Core\Model\ItemInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;

/**
 * @template ItemClass of ItemInterface
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
     * @return ?ItemClass
     */
    public function fetchById(int|string $id);

    public function countAll(): int;

    /**
     * @param array{page?:int,itemsPerPage?:int,sorting?:array<string,"ASC"|"DESC">}|array{limit?:int,offset?:int,sorting?:array<string,"ASC"|"DESC">}|null $navigation
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
     * @param array{page?:int,itemsPerPage?:int,sorting?:array<string,"ASC"|"DESC">}|array{limit?:int,offset?:int,sorting?:array<string,"ASC"|"DESC">}|null $navigation
     *
     * @return array<ItemClass>
     */
    public function fetchFiltered(array $filters, ?array $navigation = null): array;

    /**
     * @param array<string,mixed> $filters
     * @param array{page?:int,itemsPerPage?:int,sorting?:array<string,"ASC"|"DESC">}|array{limit?:int,offset?:int,sorting?:array<string,"ASC"|"DESC">}|null $navigation
     *
     * @return ?ItemClass
     */
    public function fetchOneFiltered(array $filters, ?array $navigation = null);

    /**
     * @param array<int|string> $ids
     *
     * @return array<ItemClass>
     */
    public function fetchByIdList(array $ids): array;

    public static function getSchema(): ContainerSchema;
}
