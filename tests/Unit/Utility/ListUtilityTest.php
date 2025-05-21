<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Utility;

use DigitalMarketingFramework\Core\Utility\ListUtility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ListUtilityTest extends TestCase
{
    /**
     * @param array<mixed> $expected
     * @param array<string,array{uuid:string,weight:int,value:mixed}> $actualList
     */
    protected static function assertValuesEqual(array $expected, array $actualList): void
    {
        $actualValues = array_values(array_map(static fn (array $item) => $item[ListUtility::KEY_VALUE], $actualList));
        static::assertEquals($expected, $actualValues);
    }

    /**
     * @param array<mixed> $expected
     * @param array<string,array{uuid:string,weight:int,value:mixed}> $actualList
     */
    protected static function assertWeightsEqual(array $expected, array $actualList): void
    {
        $actualWeights = array_values(array_map(static fn (array $item) => $item[ListUtility::KEY_WEIGHT], $actualList));
        static::assertEquals($expected, $actualWeights);
    }

    /**
     * @return array{uuid:string,weight:int,value:mixed}
     */
    protected static function createItem(mixed $value, string $id, int $weight = 0): array
    {
        return [
            ListUtility::KEY_UID => $id,
            ListUtility::KEY_VALUE => $value,
            ListUtility::KEY_WEIGHT => $weight,
        ];
    }

    /**
     * @return array<array{0:array<string,array{uuid:string,weight:int,value:mixed}>,1:array<mixed>}>
     */
    public static function flattenDataProvider(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                ['A' => [ListUtility::KEY_UID => 'A', ListUtility::KEY_WEIGHT => 0, ListUtility::KEY_VALUE => 'foo']],
                ['foo'],
            ],
            [
                [
                    'A' => [ListUtility::KEY_UID => 'A', ListUtility::KEY_WEIGHT => 0, ListUtility::KEY_VALUE => 'foo'],
                    'B' => [ListUtility::KEY_UID => 'B', ListUtility::KEY_WEIGHT => 0, ListUtility::KEY_VALUE => 'bar'],
                ],
                ['foo', 'bar'],
            ],
        ];
    }

    /**
     * @param array<string,array{uuid:string,weight:int,value:mixed}> $list
     * @param array<mixed> $expected
     */
    #[Test]
    #[DataProvider('flattenDataProvider')]
    public function flatten(array $list, array $expected): void
    {
        $result = ListUtility::flatten($list);
        static::assertEquals($expected, $result);
    }

    /**
     * @return array<array{0:array<string,array{uuid:string,weight:int,value:mixed}>,1:string,2:array<mixed>,3:array<mixed>,4:array<int>}>
     */
    public static function insertMultipleBeforeDataProvider(): array
    {
        return [
            'newValueFits' => [
                [
                    'A' => static::createItem('a', 'A', 0),
                    'B' => static::createItem('b', 'B', 10),
                    'C' => static::createItem('c', 'C', 15),
                    'D' => static::createItem('d', 'D', 20),
                ],
                'C',
                ['e'],
                ['a', 'b', 'e', 'c', 'd'],
                [0, 10, 13, 15, 20],
            ],
            'newValueBarelyFits' => [
                [
                    'A' => static::createItem('a', 'A', 0),
                    'B' => static::createItem('b', 'B', 10),
                    'C' => static::createItem('c', 'C', 12),
                    'D' => static::createItem('d', 'D', 20),
                ],
                'C',
                ['e'],
                ['a', 'b', 'e', 'c', 'd'],
                [0, 10, 11, 12, 20],
            ],
            'newValueDoesNotFit' => [
                [
                    'A' => static::createItem('a', 'A', 0),
                    'B' => static::createItem('b', 'B', 10),
                    'C' => static::createItem('c', 'C', 11),
                    'D' => static::createItem('d', 'D', 20),
                ],
                'C',
                ['e'],
                ['a', 'b', 'e', 'c', 'd'],
                [0, 4, 8, 11, 20],
            ],
            'newValueDoesNotEvenFitWithNeighbour' => [
                [
                    'A' => static::createItem('a', 'A', 10),
                    'B' => static::createItem('b', 'B', 20),
                    'C' => static::createItem('c', 'C', 21),
                    'D' => static::createItem('d', 'D', 22),
                    'E' => static::createItem('e', 'E', 23),
                    'F' => static::createItem('f', 'F', 33),
                ],
                'D',
                ['g'],
                ['a', 'b', 'c', 'g', 'd', 'e', 'f'],
                [10, 20, 21, 24, 27, 30, 33],
            ],
            'newValueDoesNotFitAtAll' => [
                [
                    'A' => static::createItem('a', 'A', 10),
                    'B' => static::createItem('b', 'B', 11),
                    'C' => static::createItem('c', 'C', 12),
                    'D' => static::createItem('d', 'D', 13),
                ],
                'C',
                ['g'],
                ['a', 'b', 'g', 'c', 'd'],
                [10, 11, 11 + ListUtility::WEIGHT_DELTA, 11 + ListUtility::WEIGHT_DELTA * 2, 11 + ListUtility::WEIGHT_DELTA * 3],
            ],
        ];
    }

    /**
     * @param array<string,array{uuid:string,weight:int,value:mixed}> $list
     * @param array<mixed> $values
     * @param array<mixed> $expectedValues
     * @param array<int> $expectedWeights
     */
    #[Test]
    #[DataProvider('insertMultipleBeforeDataProvider')]
    public function insertMultipleBefore(array $list, string $id, array $values, array $expectedValues, array $expectedWeights): void
    {
        $result = ListUtility::sort(ListUtility::insertMultipleBefore($list, $id, $values));
        static::assertValuesEqual($expectedValues, $result);
        static::assertWeightsEqual($expectedWeights, $result);
    }

    /**
     * @return array<array{0:array<string,array{uuid:string,weight:int,value:mixed}>,1:array<mixed>,2:array<mixed>,3:array<int>}>
     */
    public static function appendMultipleDataProvider(): array
    {
        return [
            'appendToEmptyList' => [
                [],
                ['a'],
                ['a'],
                [ListUtility::WEIGHT_START],
            ],
            'appendToNonEmptyList' => [
                [
                    'A' => static::createItem('a', 'A', 10),
                ],
                ['b'],
                ['a', 'b'],
                [10, 10 + ListUtility::WEIGHT_DELTA],
            ],
        ];
    }

    /**
     * @param array<string,array{uuid:string,weight:int,value:mixed}> $list
     * @param array<mixed> $values
     * @param array<mixed> $expectedValues
     * @param array<int> $expectedWeights
     */
    #[Test]
    #[DataProvider('appendMultipleDataProvider')]
    public function appendMultiple(array $list, array $values, array $expectedValues, $expectedWeights): void
    {
        $result = ListUtility::appendMultiple($list, $values);
        static::assertValuesEqual($expectedValues, $result);
        static::assertWeightsEqual($expectedWeights, $result);
    }
}
