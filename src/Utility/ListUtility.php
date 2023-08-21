<?php

namespace DigitalMarketingFramework\Core\Utility;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

class ListUtility extends AbstractListUtility
{
    public static function flatten(array $list, bool $sort = true): array
    {
        if ($sort) {
            $list = static::sort($list);
        }
        $result = [];
        foreach ($list as $item) {
            $result[] = $item[static::KEY_VALUE];
        }
        return $result;
    }

    public static function createItem(mixed $value, int $weight = 0, string $id = ''): array
    {
        return [
            static::KEY_UID => $id !== '' ? $id : ConfigurationUtility::generateUuid(),
            static::KEY_WEIGHT => $weight,
            static::KEY_VALUE => $value,
        ];
    }

    protected static function addValues(array &$list, array $values): array
    {
        $ids = [];
        foreach ($values as $value) {
            $item = static::createItem($value);
            $list[$item[static::KEY_UID]] = $item;
            $ids[] = $item[static::KEY_UID];
        }
        return $ids;
    }

    public static function append(array $list, mixed $value): array
    {
        return static::appendMultiple($list, [$value]);
    }

    public static function appendMultiple(array $list, array $values): array
    {
        $ids = static::addValues($list, $values);
        return static::moveMultipleToEnd($list, $ids);
    }

    public static function prepend(array $list, mixed $value): array
    {
        return static::prependMultiple($list, [$value]);
    }

    public static function prependMultiple(array $list, array $values): array
    {
        $ids = static::addValues($list, $values);
        return static::moveMultipleToFront($list, $ids);
    }

    public static function insertAfter(array $list, string $id, mixed $value): array
    {
        return static::insertMultipleAfter($list, $id, [$value]);
    }

    public static function insertMultipleAfter(array $list, string $id, array $values): array
    {
        $ids = static::addValues($list, $values);
        return static::moveMultipleAfter($list, $ids, $id);
    }

    public static function insertBefore(array $list, string $id, mixed $value): array
    {
        return static::insertMultipleBefore($list, $id, [$value]);
    }

    public static function insertMultipleBefore(array $list, string $id, array $values): array
    {
        $ids = static::addValues($list, $values);
        return static::moveMultipleBefore($list, $ids, $id);
    }
}
