<?php

namespace DigitalMarketingFramework\Core\Utility;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

/**
 * @template Item of array{uuid:string,weight:int,value:mixed}
 */
abstract class AbstractListUtility
{
    public const KEY_UID = 'uuid';

    public const KEY_WEIGHT = 'weight';

    public const KEY_VALUE = 'value';

    public const WEIGHT_DELTA = 100;

    public const WEIGHT_START = 10000;

    /**
     * @param Item $item
     */
    public static function getItemId(array $item): string
    {
        return $item[static::KEY_UID];
    }

    /**
     * @param Item $item
     */
    public static function getItemValue(array $item): mixed
    {
        return $item[static::KEY_VALUE];
    }

    /**
     * @param Item $item
     */
    public static function getItemWeight(array $item): int
    {
        return $item[static::KEY_WEIGHT];
    }

    /**
     * @param array<string,Item> $list
     *
     * @return array<string,Item>
     */
    public static function sort(array $list): array
    {
        uasort($list, static function (array $a, array $b) {
            return $a[static::KEY_WEIGHT] <=> $b[static::KEY_WEIGHT];
        });

        return $list;
    }

    /**
     * @param array<string,Item> $list
     * @param array<string> $idsToRemove
     *
     * @return array<string,Item>
     */
    public static function removeMultiple(array $list, array $idsToRemove): array
    {
        return array_filter($list, static function (array $item) use ($idsToRemove) {
            return !in_array($item[static::KEY_UID], $idsToRemove);
        });
    }

    /**
     * @param array<string,Item> $list
     *
     * @return array<string,Item>
     */
    public static function remove(array $list, string $id): array
    {
        return static::removeMultiple($list, [$id]);
    }

    /**
     * @param array<string,Item> $list
     *
     * @return ?Item
     */
    public static function findLast(array $list): ?array
    {
        $list = array_reverse(static::sort($list), true);
        $item = reset($list);

        return $item === false ? null : $item;
    }

    /**
     * @param array<string,Item> $list
     *
     * @return ?Item
     */
    public static function findFirst(array $list): ?array
    {
        $list = static::sort($list);
        $item = reset($list);

        return $item === false ? null : $item;
    }

    /**
     * @param array<string,Item> $list
     *
     * @return ?Item
     */
    public static function findPredecessor(array $list, string $id, int $amount = 1): ?array
    {
        $list = static::sort($list);
        $keys = array_keys($list);
        $positions = array_flip($keys);
        $position = $positions[$id];
        if ($position < $amount) {
            return null;
        }

        return $list[$keys[$position - $amount]];
    }

    /**
     * @param array<string,Item> $list
     *
     * @return array<string,Item>
     */
    public static function findAllPredecessors(array $list, string $id, bool $includeInitialItem = false): array
    {
        $list = static::sort($list);
        $result = [];
        foreach ($list as $itemId => $item) {
            if ($itemId === $id && !$includeInitialItem) {
                break;
            }

            $result[$itemId] = $item;
            if ($itemId === $id && $includeInitialItem) {
                break;
            }
        }

        /** @var array<string,Item> */
        return $result;
    }

    /**
     * @param array<string,Item> $list
     *
     * @return ?Item
     */
    public static function findSuccessor(array $list, string $id, int $amount = 1): ?array
    {
        $list = static::sort($list);
        $keys = array_keys($list);
        $positions = array_flip($keys);
        $position = $positions[$id];
        if ($position + $amount === count($list)) {
            return null;
        }

        return $list[$keys[$position + $amount]];
    }

    /**
     * @param array<string,Item> $list
     *
     * @return array<string,Item>
     */
    public static function findAllSuccessors(array $list, string $id, bool $includeInitialItem = false): array
    {
        $list = static::sort($list);
        $result = [];
        $idFound = false;
        foreach ($list as $itemId => $item) {
            if ($itemId === $id) {
                $idFound = true;
                if (!$includeInitialItem) {
                    continue;
                }
            }

            if ($idFound) {
                $result[$itemId] = $item;
            }
        }

        /** @var array<string,Item> */
        return $result;
    }

    /**
     * @param array<string,Item> $list
     * @param array<string> $ids
     *
     * @return array<string,Item>
     */
    public static function moveMultipleToEnd(array $list, array $ids): array
    {
        $lastItem = static::findLast(static::removeMultiple($list, $ids));
        $weight = $lastItem !== null ? $lastItem[static::KEY_WEIGHT] + static::WEIGHT_DELTA : static::WEIGHT_START;
        foreach ($ids as $id) {
            $list[$id][static::KEY_WEIGHT] = $weight;
            $weight += static::WEIGHT_DELTA;
        }

        /** @var array<string,Item> */
        return $list;
    }

    /**
     * @param array<string,Item> $list
     *
     * @return array<string,Item>
     */
    public static function moveToEnd(array $list, string $id): array
    {
        return static::moveMultipleToEnd($list, [$id]);
    }

    /**
     * @param array<string,Item> $list
     *
     * @return array<string,Item>
     */
    public static function moveBefore(array $list, string $id, string $beforeId): array
    {
        return static::moveMultipleBefore($list, [$id], $beforeId);
    }

    /**
     * @param array<string,Item> $list
     * @param array<string> $ids
     *
     * @return array<string,Item>
     */
    public static function moveMultipleBefore(array $list, array $ids, string $beforeId): array
    {
        $previousItem = static::findPredecessor(static::removeMultiple($list, $ids), $beforeId);
        if ($previousItem === null) {
            return static::moveMultipleToFront($list, $ids);
        }

        return static::moveMultipleBetween($list, $previousItem[static::KEY_UID], $beforeId, $ids);
    }

    /**
     * @param array<string,Item> $list
     * @param array<string> $ids
     *
     * @return array<string,Item>
     */
    public static function moveMultipleToFront(array $list, array $ids): array
    {
        $firstItem = static::findFirst(static::removeMultiple($list, $ids));
        $weight = $firstItem !== null ? $firstItem[static::KEY_WEIGHT] - static::WEIGHT_DELTA : static::WEIGHT_START;
        foreach ($ids as $id) {
            $list[$id][static::KEY_WEIGHT] = $weight;
            $weight -= static::WEIGHT_DELTA;
        }

        /** @var array<string,Item> */
        return $list;
    }

    /**
     * @param array<string,Item> $list
     *
     * @return array<string,Item>
     */
    public static function moveToFront(array $list, string $id): array
    {
        return static::moveMultipleToFront($list, [$id]);
    }

    /**
     * @param array<string,Item> $list
     * @param array<string> $ids
     *
     * @return array<string,Item>
     */
    public static function moveMultipleAfter(array $list, array $ids, string $afterId): array
    {
        $nextItem = static::findSuccessor(static::removeMultiple($list, $ids), $afterId);
        if ($nextItem === null) {
            return static::moveMultipleToEnd($list, $ids);
        }

        return static::moveMultipleBetween($list, $afterId, $nextItem[static::KEY_UID], $ids);
    }

    /**
     * @param array<string,Item> $list
     *
     * @return array<string,Item>
     */
    public static function moveAfter(array $list, string $id, string $afterId): array
    {
        return static::moveMultipleAfter($list, [$id], $afterId);
    }

    /**
     * @param array<string,Item> $list
     *
     * @return array<string,Item>
     */
    public static function moveBetween(array $list, string $previousId, string $nextId, string $id): array
    {
        return static::moveMultipleBetween($list, $previousId, $nextId, [$id]);
    }

    /**
     * @param array<string,Item> $list
     * @param array<string> $ids
     *
     * @return array<string,Item>
     */
    public static function moveMultipleBetween(array $list, string $previousId, string $nextId, array $ids): array
    {
        $reducedList = static::removeMultiple($list, $ids);
        $allPreviousIds = array_keys(static::findAllPredecessors($reducedList, $previousId, true));
        $allNextIds = array_keys(static::findAllSuccessors($reducedList, $nextId, true));
        $minItems = count($ids);
        $maxItems = min(count($allPreviousIds), count($allNextIds)) + count($ids);

        $winnerPreviousId = null;
        $winnerIds = null;
        $winnerNextId = null;
        $winnerCount = null;
        $winnerRange = null;

        $previousIds = $allPreviousIds;
        $currentPreviousIds = [];
        while ($previousIds !== []) {
            $currentPreviousId = array_pop($previousIds);

            $nextIds = $allNextIds;
            $currentNextIds = [];
            while ($nextIds !== []) {
                $currentNextId = array_shift($nextIds);
                $currentIds = [...$currentPreviousIds, ...$ids, ...$currentNextIds];
                $currentCount = count($currentIds);
                $currentRange = $list[$currentNextId][static::KEY_WEIGHT] - $list[$currentPreviousId][static::KEY_WEIGHT];

                if (
                    // if it is easier to rearrange all items in one direction, don't bother
                    $currentCount <= $maxItems

                    // if there is already a smaller fitting subset, don't bother
                    && (
                        $winnerIds === null // no other fitting subset
                        || $currentCount < $winnerCount // other fitting subset is larget
                        || ($currentCount === $winnerCount && $currentRange > $winnerRange) // other fitting subset is equal in size but has a smaller weight range
                    )

                    // if this subset does not fit, don't bother
                    && $currentRange > $currentCount
                ) {
                    $winnerIds = $currentIds;
                    $winnerPreviousId = $currentPreviousId;
                    $winnerNextId = $currentNextId;
                    $winnerCount = $currentCount;
                    $winnerRange = $currentRange;
                    break;
                }

                $currentNextIds[] = $currentNextId;
            }

            if ($winnerIds !== null && $winnerCount === $minItems) {
                break; // if we have the smallest possible set already, which there can only be one of, then stop
            }

            $currentPreviousIds[] = $currentPreviousId;
        }

        if ($winnerIds === null) {
            if (count($allPreviousIds) < count($allNextIds)) {
                return static::moveMultipleToFront($list, [...$allPreviousIds, ...$ids]);
            }

            return static::moveMultipleToEnd($list, [...$ids, ...$allNextIds]);
        }

        $previousWeight = $list[$winnerPreviousId][static::KEY_WEIGHT];
        $nextWeight = $list[$winnerNextId][static::KEY_WEIGHT];
        $delta = ceil(($nextWeight - $previousWeight) / (count($winnerIds) + 1));
        $weight = $previousWeight + $delta;
        foreach ($winnerIds as $id) {
            $list[$id][static::KEY_WEIGHT] = $weight;
            $weight += $delta;
        }

        /** @var array<string,Item> */
        return $list;
    }

    protected static function containerName(): string
    {
        return 'list';
    }

    /**
     * @param array<mixed> $list
     */
    public static function validate(array $list, int $attributeCount = 3): void
    {
        foreach ($list as $id => $item) {
            if (!is_array($item)) {
                throw new DigitalMarketingFrameworkException(sprintf('%s item "%s" is not an array', static::containerName(), $id));
            }

            if (!isset($item[static::KEY_UID])) {
                throw new DigitalMarketingFrameworkException(sprintf('%s item "%s" does not have a uuid', static::containerName(), $id));
            }

            if ($item[static::KEY_UID] !== $id) {
                throw new DigitalMarketingFrameworkException(sprintf('%s item "%s" uuid "%s" is inconsistent', static::containerName(), $id, $item[static::KEY_UID]));
            }

            if (!isset($item[static::KEY_VALUE])) {
                throw new DigitalMarketingFrameworkException(sprintf('%s item "%s" does not have a value', static::containerName(), $id));
            }

            if (!isset($item[static::KEY_WEIGHT])) {
                throw new DigitalMarketingFrameworkException(sprintf('%s item "%s" does not have a weight attribute', static::containerName(), $id));
            }

            if (!is_numeric($item[static::KEY_WEIGHT])) {
                throw new DigitalMarketingFrameworkException(sprintf('%s item "%s" weight attribute it not numeric', static::containerName(), $id));
            }

            if (count(array_keys($item)) !== $attributeCount) {
                throw new DigitalMarketingFrameworkException(sprintf('%s item "%s" has too many attributes', static::containerName(), $id));
            }
        }
    }
}
