<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Utility;

use DigitalMarketingFramework\Core\Utility\ListUtility;
use PHPUnit\Framework\TestCase;

class ListUtilityTest extends TestCase
{
    /**
     * @param array<mixed> $expected
     * @param array<string,array{uuid:string,weight:int,value:mixed}> $actualList
     */
    protected static function assertValuesEqual(array $expected, array $actualList): void
    {
        $actualValues = array_values(array_map(static function (array $item) {
            return $item[ListUtility::KEY_VALUE];
        }, $actualList));
        static::assertEquals($expected, $actualValues);
    }

    /**
     * @param array<mixed> $expected
     * @param array<string,array{uuid:string,weight:int,value:mixed}> $actualList
     */
    protected static function assertWeightsEqual(array $expected, array $actualList): void
    {
        $actualWeights = array_values(array_map(static function (array $item) {
            return $item[ListUtility::KEY_WEIGHT];
        }, $actualList));
        static::assertEquals($expected, $actualWeights);
    }

    /**
     * @return array{uuid:string,weight:int,value:mixed}
     */
    protected function createItem(mixed $value, string $id, int $weight = 0): array
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
    public function flattenDataProvider(): array
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
     *
     * @dataProvider flattenDataProvider
     *
     * @test
     */
    public function flatten(array $list, array $expected): void
    {
        $result = ListUtility::flatten($list);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0:array<string,array{uuid:string,weight:int,value:mixed}>,1:string,2:array<mixed>,3:array<mixed>,4:array<int>}>
     */
    public function insertMultipleBeforeDataProvider(): array
    {
        return [
            'newValueFits' => [
                [
                    'A' => $this->createItem('a', 'A', 0),
                    'B' => $this->createItem('b', 'B', 10),
                    'C' => $this->createItem('c', 'C', 15),
                    'D' => $this->createItem('d', 'D', 20),
                ],
                'C',
                ['e'],
                ['a', 'b', 'e', 'c', 'd'],
                [0, 10, 13, 15, 20],
            ],
            'newValueBarelyFits' => [
                [
                    'A' => $this->createItem('a', 'A', 0),
                    'B' => $this->createItem('b', 'B', 10),
                    'C' => $this->createItem('c', 'C', 12),
                    'D' => $this->createItem('d', 'D', 20),
                ],
                'C',
                ['e'],
                ['a', 'b', 'e', 'c', 'd'],
                [0, 10, 11, 12, 20],
            ],
            'newValueDoesNotFit' => [
                [
                    'A' => $this->createItem('a', 'A', 0),
                    'B' => $this->createItem('b', 'B', 10),
                    'C' => $this->createItem('c', 'C', 11),
                    'D' => $this->createItem('d', 'D', 20),
                ],
                'C',
                ['e'],
                ['a', 'b', 'e', 'c', 'd'],
                [0, 4, 8, 11, 20],
            ],
            'newValueDoesNotEvenFitWithNeighbour' => [
                [
                    'A' => $this->createItem('a', 'A', 10),
                    'B' => $this->createItem('b', 'B', 20),
                    'C' => $this->createItem('c', 'C', 21),
                    'D' => $this->createItem('d', 'D', 22),
                    'E' => $this->createItem('e', 'E', 23),
                    'F' => $this->createItem('f', 'F', 33),
                ],
                'D',
                ['g'],
                ['a', 'b', 'c', 'g', 'd', 'e', 'f'],
                [10, 20, 21, 24, 27, 30, 33],
            ],
            'newValueDoesNotFitAtAll' => [
                [
                    'A' => $this->createItem('a', 'A', 10),
                    'B' => $this->createItem('b', 'B', 11),
                    'C' => $this->createItem('c', 'C', 12),
                    'D' => $this->createItem('d', 'D', 13),
                ],
                'C',
                ['g'],
                ['a', 'b', 'g', 'c', 'd'],
                [10, 11, 11 + ListUtility::WEIGHT_DELTA, 11 + ListUtility::WEIGHT_DELTA * 2, 11 + ListUtility::WEIGHT_DELTA * 3],
            ],
        ];
    }

    /**
     * @test
     *
     * @param array<string,array{uuid:string,weight:int,value:mixed}> $list
     * @param array<mixed> $values
     * @param array<mixed> $expectedValues
     * @param array<int> $expectedWeights
     *
     * @dataProvider insertMultipleBeforeDataProvider
     */
    public function insertMultipleBefore(array $list, string $id, array $values, array $expectedValues, array $expectedWeights): void
    {
        $result = ListUtility::sort(ListUtility::insertMultipleBefore($list, $id, $values));
        $this->assertValuesEqual($expectedValues, $result);
        $this->assertWeightsEqual($expectedWeights, $result);
    }

    /**
     * @return array<array{0:array<string,array{uuid:string,weight:int,value:mixed}>,1:array<mixed>,2:array<mixed>,3:array<int>}>
     */
    public function appendMultipleDataProvider(): array
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
                    'A' => $this->createItem('a', 'A', 10),
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
     *
     * @test
     *
     * @dataProvider appendMultipleDataProvider
     */
    public function appendMultiple(array $list, array $values, array $expectedValues, $expectedWeights): void
    {
        $result = ListUtility::appendMultiple($list, $values);
        $this->assertValuesEqual($expectedValues, $result);
        $this->assertWeightsEqual($expectedWeights, $result);
    }
}
