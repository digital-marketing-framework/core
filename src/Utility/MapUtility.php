<?php

namespace DigitalMarketingFramework\Core\Utility;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

/**
 * @extends AbstractListUtility<array{uuid:string,weight:int,key:string,value:mixed}>
 */
class MapUtility extends AbstractListUtility
{
    public const KEY_KEY = 'key';

    /**
     * @param array{uuid:string,weight:int,key:string,value:mixed} $item
     */
    public static function getItemKey(array $item): string
    {
        return $item[static::KEY_KEY];
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     *
     * @return array<string,mixed>
     */
    public static function flatten(array $list, bool $sort = true): array
    {
        if ($sort) {
            $list = static::sort($list);
        }

        $result = [];
        foreach ($list as $item) {
            /** @var string */
            $key = $item[static::KEY_KEY];
            $result[$key] = $item[static::KEY_VALUE];
        }

        return $result;
    }

    /**
     * @return array{uuid:string,weight:int,key:string,value:mixed}
     */
    public static function createItem(mixed $value, string $key, int $weight = 0, string $id = ''): array
    {
        /** @var array{uuid:string,weight:int,key:string,value:mixed} */
        return [
            static::KEY_UID => $id !== '' ? $id : ConfigurationUtility::generateUuid(),
            static::KEY_WEIGHT => $weight,
            static::KEY_KEY => $key,
            static::KEY_VALUE => $value,
        ];
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     * @param array<string,mixed> $values
     *
     * @return array<string>
     */
    protected static function addValues(array &$list, array $values): array
    {
        $ids = [];
        foreach ($values as $key => $value) {
            $item = static::createItem($value, $key);
            $list[$item[static::KEY_UID]] = $item;
            $ids[] = $item[static::KEY_UID];
        }

        return $ids;
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     *
     * @return array<string,array{uuid:string,weight:int,key:string,value:mixed}>
     */
    public static function append(array $list, string $key, mixed $value): array
    {
        return static::appendMultiple($list, [$key => $value]);
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     * @param array<string,mixed> $values
     *
     * @return array<string,array{uuid:string,weight:int,key:string,value:mixed}>
     */
    public static function appendMultiple(array $list, array $values): array
    {
        $ids = static::addValues($list, $values);

        return static::moveMultipleToEnd($list, $ids);
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     *
     * @return array<string,array{uuid:string,weight:int,key:string,value:mixed}>
     */
    public static function prepend(array $list, string $key, mixed $value): array
    {
        return static::prependMultiple($list, [$key => $value]);
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     * @param array<string,mixed> $values
     *
     * @return array<string,array{uuid:string,weight:int,key:string,value:mixed}>
     */
    public static function prependMultiple(array $list, array $values): array
    {
        $ids = static::addValues($list, $values);

        return static::moveMultipleToFront($list, $ids);
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     *
     * @return array<string,array{uuid:string,weight:int,key:string,value:mixed}>
     */
    public static function insertAfter(array $list, string $id, string $key, mixed $value): array
    {
        return static::insertMultipleAfter($list, $id, [$key => $value]);
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     * @param array<string,mixed> $values
     *
     * @return array<string,array{uuid:string,weight:int,key:string,value:mixed}>
     */
    public static function insertMultipleAfter(array $list, string $id, array $values): array
    {
        $ids = static::addValues($list, $values);

        return static::moveMultipleAfter($list, $ids, $id);
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     *
     * @return array<string,array{uuid:string,weight:int,key:string,value:mixed}>
     */
    public static function insertBefore(array $list, string $id, string $key, mixed $value): array
    {
        return static::insertMultipleBefore($list, $id, [$key => $value]);
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     * @param array<string,mixed> $values
     *
     * @return array<string,array{uuid:string,weight:int,key:string,value:mixed}>
     */
    public static function insertMultipleBefore(array $list, string $id, array $values): array
    {
        $ids = static::addValues($list, $values);

        return static::moveMultipleBefore($list, $ids, $id);
    }

    protected static function containerName(): string
    {
        return 'map';
    }

    /**
     * @param array<mixed> $list
     *
     * @throws DigitalMarketingFrameworkException
     */
    public static function validate(array $list, int $attributeCount = 4): void
    {
        parent::validate($list, $attributeCount);
        foreach ($list as $id => $item) {
            if (!isset($item[static::KEY_KEY])) {
                throw new DigitalMarketingFrameworkException(sprintf('%s item "%s" does not have a key attribute', static::containerName(), $id));
            }
        }
    }
}
