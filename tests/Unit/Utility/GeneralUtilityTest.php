<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Utility;

use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use PHPUnit\Framework\TestCase;

class GeneralUtilityTest extends TestCase
{
    use MultiValueTestTrait;

    /**
     * @return array<array{0:mixed,1:bool}>
     */
    public function valueIsEmptyProvider(): array
    {
        return [
            [null, true],
            ['', true],
            [0, false],
            [1, false],
            ['0', false],
            ['1', false],
            ['value1', false],
            [new MultiValue(['']), false],
            [new MultiValue([]), true],
            [new MultiValue(['0']), false],
            [new MultiValue(['1']), false],
            [new MultiValue(['value1']), false],
        ];
    }

    /**
     * @dataProvider valueIsEmptyProvider
     *
     * @test
     */
    public function valueIsEmpty(mixed $value, bool $expected): void
    {
        $result = GeneralUtility::isEmpty($value);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @return array<array{0:mixed,1:bool}>
     */
    public function valueIsTrueProvider(): array
    {
        return [
            [null, false],
            ['', false],
            [0, false],
            [1, true],
            ['0', false],
            ['1', true],
            ['value1', true],
            [new MultiValue([]), false],
            [new MultiValue(['0']), true],
            [new MultiValue(['1']), true],
            [new MultiValue(['5']), true],
            [new MultiValue(['']), true],
            [new MultiValue(['value1']), true],
        ];
    }

    /**
     * @dataProvider valueIsTrueProvider
     *
     * @test
     */
    public function valueIsTrue(mixed $value, bool $expected): void
    {
        $result = GeneralUtility::isTrue($value);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @dataProvider valueIsTrueProvider
     *
     * @test
     */
    public function valueIsFalse(mixed $value, bool $notExpected): void
    {
        $result = GeneralUtility::isFalse($value);
        if ($notExpected) {
            $this->assertFalse($result);
        } else {
            $this->assertTrue($result);
        }
    }

    /**
     * @return array<array{0:string,1:string}>
     */
    public function parseSeparatorStringProvider(): array
    {
        return [
            ['', ''],
            [' ', ''],
            [' value1 ', 'value1'],
            ['\\s', ' '],
            ['\\t', "\t"],
            ['\\n', "\n"],
            ['\\s\\t\\n\\t\\s', " \t\n\t "],
        ];
    }

    /**
     * @dataProvider parseSeparatorStringProvider
     *
     * @test
     */
    public function parseSeparatorString(string $value, string $expected): void
    {
        $result = GeneralUtility::parseSeparatorString($value);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0:mixed,1:bool}>
     */
    public function isListProvider(): array
    {
        return [
            [null, false],
            ['', false],
            [0, false],
            [1, false],
            ['0', false],
            ['1', false],
            ['value1', false],
            [[], true],
            [[''], true],
            [['value1'], true],
            [new MultiValue(), true],
            [new MultiValue(['value1']), true],
        ];
    }

    /**
     * @dataProvider isListProvider
     *
     * @test
     */
    public function testIsList(mixed $value, bool $expected): void
    {
        $result = GeneralUtility::isList($value);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @return array<array{0:mixed,1:?string,2:?bool,3:mixed}>
     */
    public function castValueToArrayProvider(): array
    {
        return [
            [[], null, null, []],
            [['value1'], null, null, ['value1']],
            [[' value1 '], ',', true, ['value1']],
            [new MultiValue([]), null, null, []],
            [new MultiValue(['value1', 'value2']), null, null, ['value1', 'value2']],
            [new MultiValue([' value1', 'value2 ']), null, null, ['value1', 'value2']],

            ['', null, null, []],
            ['value1', null, null, ['value1']],
            ['value1,value2', null, null, ['value1', 'value2']],
            ['value1, value2', null, null, ['value1', 'value2']],
            ['value1, value2', ',', false, ['value1', ' value2']],
            ['value1;value2', ';', null, ['value1', 'value2']],
            ['value1; value2', ';', null, ['value1', 'value2']],
            ['value1; value2', ';', false, ['value1', ' value2']],
        ];
    }

    /**
     * @dataProvider castValueToArrayProvider
     *
     * @test
     */
    public function castValueToArray(mixed $value, ?string $token, ?bool $trim, mixed $expected): void
    {
        if ($token === null && $trim === null) {
            $result = GeneralUtility::castValueToArray($value);
        } elseif ($trim === null) {
            $result = GeneralUtility::castValueToArray($value, $token);
        } else {
            $result = GeneralUtility::castValueToArray($value, $token, $trim);
        }

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0:array<string,string|ValueInterface>,1:bool,2:string}>
     */
    public function calculateHashProvider(): array
    {
        return [
            [[], false, 'undefined'],
            [[], true, 'undefined'],
            [['key1' => 'value1'], false, 'E2E517365FFE6FEDD279364E3FA74786'],
            [['key1' => 'value1'], true, 'E2E51'],
        ];
    }

    /**
     * @param array<string,string|ValueInterface> $submission
     *
     * @dataProvider calculateHashProvider
     *
     * @test
     */
    public function calculateHash(array $submission, bool $short, string $expected): void
    {
        $result = GeneralUtility::calculateHash($submission, $short);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0:string,1:string}>
     */
    public function shortenHashProvider(): array
    {
        return [
            ['', ''],
            ['A', 'A'],
            ['ABCDE', 'ABCDE'],
            ['ABCDEF', 'ABCDE'],
            ['ABCDEFGHIJKLM', 'ABCDE'],
        ];
    }

    /**
     * @dataProvider shortenHashProvider
     *
     * @test
     */
    public function shortenHash(string $hash, string $expected): void
    {
        $result = GeneralUtility::shortenHash($hash);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0:mixed,1:mixed,2:bool}>
     */
    public function compareValueProvider(): array
    {
        // values in one group are considered to be equal
        $valueGroups = [
            [null, ''],
            [0, '0'],
            [1, '1'],
            [5, '5'],
            ['value1'],
            ['value2'],
        ];
        $provided = [];
        foreach ($valueGroups as $groupIndex => $valueGroup) {
            foreach ($valueGroup as $value) {
                foreach ($valueGroups as $groupIndex2 => $valueGroup2) {
                    foreach ($valueGroup2 as $value2) {
                        $provided[] = [$value, $value2, $groupIndex === $groupIndex2];
                    }
                }
            }
        }

        return $provided;
    }

    /**
     * @dataProvider compareValueProvider
     *
     * @test
     */
    public function compareValue(mixed $fieldValue, mixed $compareValue, bool $expected): void
    {
        $result = GeneralUtility::compareValue($fieldValue, $compareValue);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param array<array<mixed>> $valueGroups
     *
     * @return array<array{0:mixed,1:mixed,2:bool}>
     */
    protected function generateComparisonPairs(array $valueGroups): array
    {
        // values in one group are considered to be equal
        $provided = [];
        foreach ($valueGroups as $groupIndex => $valueGroup) {
            foreach ($valueGroup as $value) {
                foreach ($valueGroups as $groupIndex2 => $valueGroup2) {
                    foreach ($valueGroup2 as $value2) {
                        $provided[] = [$value, $value2, $groupIndex === $groupIndex2];
                    }
                }
            }
        }

        return $provided;
    }

    /**
     * @return array<array{0:mixed,1:mixed,2:bool}>
     */
    public function compareListsProvider(): array
    {
        // values in one group are considered to be equal
        $valueGroups = [
            [new MultiValue(), ''],
            [new MultiValue(['value1'])],
            [new MultiValue(['value2'])],
            [new MultiValue(['value1', 'value2']), new MultiValue(['value2', 'value1']), 'value1,value2'],
            [new MultiValue(['5', '7', '13']), new MultiValue(['13', '7', '5']), '5,7,13'],
        ];

        return $this->generateComparisonPairs($valueGroups);
    }

    /**
     * @dataProvider compareListsProvider
     *
     * @test
     */
    public function compareLists(mixed $fieldValue, mixed $compareValue, bool $expected): void
    {
        $result = GeneralUtility::compareLists($fieldValue, $compareValue);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @dataProvider compareValueProvider
     * @dataProvider compareListsProvider
     *
     * @test
     */
    public function compare(mixed $fieldValue, mixed $compareValue, bool $expected): void
    {
        $result = GeneralUtility::compare($fieldValue, $compareValue);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @return array<array{0:mixed,1:array<mixed>,2:string|int|false}>
     */
    public function findInListProvider(): array
    {
        return [
            ['value1', ['value1', 'value2', 'value3'], 0],
            ['value2', ['value1', 'value2', 'value3'], 1],
            ['value4', ['value1', 'value2', 'value3'], false],
        ];
    }

    /**
     * @param array<mixed> $list
     *
     * @dataProvider findInListProvider
     *
     * @test
     */
    public function findInList(mixed $fieldValue, array $list, string|int|false $expected): void
    {
        $result = GeneralUtility::findInList($fieldValue, $list);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param array<mixed> $list
     *
     * @dataProvider findInListProvider
     *
     * @test
     */
    public function isInList(mixed $fieldValue, array $list, string|int|false $expected): void
    {
        $result = GeneralUtility::isInList($fieldValue, $list);
        if ($expected === false) {
            $this->assertFalse($result);
        } else {
            $this->assertTrue($result);
        }
    }

    /**
     * @return array<array{0:string,1:string,2:string}>
     */
    public function getPluginKeywordProvider(): array
    {
        return [
            'class matches interface' => ['SomeRoute', 'RouteInterface', 'some'],
            'camel case class matches interface' => ['SomeThingRoute', 'RouteInterface', 'someThing'],
            'class does not match interface' => ['SomeRoute', 'DataProviderInterface', ''],
            'class with namespace matches interface' => ['Some\\Name\\Space\\SomeThingRoute', 'RouteInterface', 'someThing'],
            'class matches interface with namespace' => ['SomeThingRoute', 'Another\\Name\\Space\\RouteInterface', 'someThing'],
            'class with namespace matches interface with namespace' => ['Some\\Name\\Space\\SomeThingRoute', 'Another\\Name\\Space\\RouteInterface', 'someThing'],
            'class with namespace does not match interface with namespace' => ['Some\\Name\\Space\\SomeThingDataProvider', 'Another\\Name\\Space\\RouteInterface', ''],
            'interface name does not end on Interface' => ['SomeRoute', 'Route', ''],
            'class matches interface both consist of multiple terms' => ['SomeLongClassNameEndingOnSomeLongInterfaceName', 'SomeLongInterfaceNameInterface', 'someLongClassNameEndingOn'],
        ];
    }

    /**
     * @dataProvider getPluginKeywordProvider
     *
     * @test
     */
    public function getPluginKeyword(string $class, string $interface, string $expected): void
    {
        $result = GeneralUtility::getPluginKeyword($class, $interface);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0:array<mixed>,1:MultiValueInterface}>
     */
    public function castArrayToMultiValueProvider(): array
    {
        return [
            'scalarValuesInArray' => [
                ['a', 'b', 'c'],
                new MultiValue(['a', 'b', 'c']),
            ],
            'nestedArray' => [
                ['a', 'b', ['c1', 'c2']],
                new MultiValue(['a', 'b', new MultiValue(['c1', 'c2'])]),
            ],
        ];
    }

    /**
     * @param array<mixed> $array
     *
     * @dataProvider castArrayToMultiValueProvider
     *
     * @test
     */
    public function castArrayToMultiValue(array $array, MultiValueInterface $expected): void
    {
        $result = GeneralUtility::castArrayToMultiValue($array);
        $this->assertMultiValueEquals($expected, $result);
    }

    /**
     * @return array<array{0:array<mixed>,1:DataInterface}>
     */
    public function castArrayToDataProvider(): array
    {
        return [
            'scalarValuesInData' => [
                ['a', 'b', 'c'],
                new Data(['a', 'b', 'c']),
            ],
            'arraysInData' => [
                ['a', 'b', ['c1', 'c2']],
                new Data(['a', 'b', new MultiValue(['c1', 'c2'])]),
            ],
            'nestedArraysInData' => [
                ['a', 'b', ['c1', ['c2.1', 'c2.2']]],
                new Data(['a', 'b', new MultiValue(['c1', new MultiValue(['c2.1', 'c2.2'])])]),
            ],
        ];
    }

    /**
     * @param array<mixed> $array
     *
     * @dataProvider castArrayToDataProvider
     *
     * @test
     */
    public function castArrayToData(array $array, DataInterface $expected): void
    {
        $result = GeneralUtility::castArrayToData($array);
        $this->assertMultiValueEquals($expected, $result);
    }
}
