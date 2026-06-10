<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Utility;

use DigitalMarketingFramework\Core\Utility\MapUtility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MapUtilityTest extends TestCase
{
    /**
     * @param array<mixed> $expected
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $actualList
     */
    protected static function assertKeysEqual(array $expected, array $actualList): void
    {
        $actualKeys = array_values(array_map(static fn (array $item) => $item[MapUtility::KEY_KEY], $actualList));
        self::assertEquals($expected, $actualKeys);
    }

    /**
     * @param array<mixed> $expected
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $actualList
     */
    protected static function assertValuesEqual(array $expected, array $actualList): void
    {
        $actualValues = array_values(array_map(static fn (array $item) => $item[MapUtility::KEY_VALUE], $actualList));
        self::assertEquals($expected, $actualValues);
    }

    /**
     * @param array<mixed> $expected
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $actualList
     */
    protected static function assertWeightsEqual(array $expected, array $actualList): void
    {
        $actualWeights = array_values(array_map(static fn (array $item) => $item[MapUtility::KEY_WEIGHT], $actualList));
        self::assertEquals($expected, $actualWeights);
    }

    /**
     * @return array{uuid:string,weight:int,key:string,value:mixed}
     */
    protected static function createItem(mixed $value, string $key, string $id, int $weight = 0): array
    {
        return [
            MapUtility::KEY_UID => $id,
            MapUtility::KEY_KEY => $key,
            MapUtility::KEY_VALUE => $value,
            MapUtility::KEY_WEIGHT => $weight,
        ];
    }

    /**
     * @return array<string,array{uuid:string,weight:int,key:string,value:mixed}>
     */
    protected static function baseList(): array
    {
        return [
            'A' => static::createItem('a', 'keyA', 'A', 10),
            'B' => static::createItem('b', 'keyB', 'B', 20),
        ];
    }

    /**
     * @return array<string,array{0:array<string,array{uuid:string,weight:int,key:string,value:mixed}>,1:array<string,mixed>,2:array<mixed>,3:array<mixed>,4:array<int>}>
     */
    public static function appendMultipleDataProvider(): array
    {
        return [
            'appendToNonEmpty' => [
                static::baseList(),
                ['keyC' => 'c'],
                ['keyA', 'keyB', 'keyC'],
                ['a', 'b', 'c'],
                [10, 20, 120],
            ],
            'appendToEmpty' => [
                [],
                ['keyC' => 'c'],
                ['keyC'],
                ['c'],
                [MapUtility::WEIGHT_START],
            ],
        ];
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     * @param array<string,mixed> $values
     * @param array<mixed> $expectedKeys
     * @param array<mixed> $expectedValues
     * @param array<int> $expectedWeights
     */
    #[Test]
    #[DataProvider('appendMultipleDataProvider')]
    public function appendMultiple(array $list, array $values, array $expectedKeys, array $expectedValues, array $expectedWeights): void
    {
        $result = MapUtility::sort(MapUtility::appendMultiple($list, $values));
        static::assertKeysEqual($expectedKeys, $result);
        static::assertValuesEqual($expectedValues, $result);
        static::assertWeightsEqual($expectedWeights, $result);
    }

    /**
     * @return array<string,array{0:array<string,array{uuid:string,weight:int,key:string,value:mixed}>,1:array<string,mixed>,2:array<mixed>,3:array<mixed>,4:array<int>}>
     */
    public static function prependMultipleDataProvider(): array
    {
        return [
            'prependToNonEmpty' => [
                static::baseList(),
                ['keyC' => 'c'],
                ['keyC', 'keyA', 'keyB'],
                ['c', 'a', 'b'],
                [-90, 10, 20],
            ],
        ];
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     * @param array<string,mixed> $values
     * @param array<mixed> $expectedKeys
     * @param array<mixed> $expectedValues
     * @param array<int> $expectedWeights
     */
    #[Test]
    #[DataProvider('prependMultipleDataProvider')]
    public function prependMultiple(array $list, array $values, array $expectedKeys, array $expectedValues, array $expectedWeights): void
    {
        $result = MapUtility::sort(MapUtility::prependMultiple($list, $values));
        static::assertKeysEqual($expectedKeys, $result);
        static::assertValuesEqual($expectedValues, $result);
        static::assertWeightsEqual($expectedWeights, $result);
    }

    /**
     * @return array<string,array{0:array<string,array{uuid:string,weight:int,key:string,value:mixed}>,1:string,2:array<string,mixed>,3:array<mixed>,4:array<mixed>,5:array<int>}>
     */
    public static function insertMultipleBeforeDataProvider(): array
    {
        return [
            // Predecessor exists → moveMultipleBetween: placed between A and B.
            'beforeMiddle' => [
                static::baseList(),
                'B',
                ['keyX' => 'x'],
                ['keyA', 'keyX', 'keyB'],
                ['a', 'x', 'b'],
                [10, 15, 20],
            ],
        ];
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     * @param array<string,mixed> $values
     * @param array<mixed> $expectedKeys
     * @param array<mixed> $expectedValues
     * @param array<int> $expectedWeights
     */
    #[Test]
    #[DataProvider('insertMultipleBeforeDataProvider')]
    public function insertMultipleBefore(array $list, string $id, array $values, array $expectedKeys, array $expectedValues, array $expectedWeights): void
    {
        $result = MapUtility::sort(MapUtility::insertMultipleBefore($list, $id, $values));
        static::assertKeysEqual($expectedKeys, $result);
        static::assertValuesEqual($expectedValues, $result);
        static::assertWeightsEqual($expectedWeights, $result);
    }

    /**
     * @return array<string,array{0:array<string,array{uuid:string,weight:int,key:string,value:mixed}>,1:string,2:array<string,mixed>,3:array<mixed>,4:array<mixed>,5:array<int>}>
     */
    public static function insertMultipleAfterDataProvider(): array
    {
        return [
            // Successor exists → moveMultipleBetween: placed between A and B.
            'afterMiddle' => [
                static::baseList(),
                'A',
                ['keyX' => 'x'],
                ['keyA', 'keyX', 'keyB'],
                ['a', 'x', 'b'],
                [10, 15, 20],
            ],
            // No successor → moveMultipleToEnd: appended after the last item.
            'afterLast' => [
                static::baseList(),
                'B',
                ['keyX' => 'x'],
                ['keyA', 'keyB', 'keyX'],
                ['a', 'b', 'x'],
                [10, 20, 120],
            ],
        ];
    }

    /**
     * @param array<string,array{uuid:string,weight:int,key:string,value:mixed}> $list
     * @param array<string,mixed> $values
     * @param array<mixed> $expectedKeys
     * @param array<mixed> $expectedValues
     * @param array<int> $expectedWeights
     */
    #[Test]
    #[DataProvider('insertMultipleAfterDataProvider')]
    public function insertMultipleAfter(array $list, string $id, array $values, array $expectedKeys, array $expectedValues, array $expectedWeights): void
    {
        $result = MapUtility::sort(MapUtility::insertMultipleAfter($list, $id, $values));
        static::assertKeysEqual($expectedKeys, $result);
        static::assertValuesEqual($expectedValues, $result);
        static::assertWeightsEqual($expectedWeights, $result);
    }
}
