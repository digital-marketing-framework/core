<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Utility;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use PHPUnit\Framework\TestCase;

class GeneralUtilityTest extends TestCase
{
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
            [new MultiValue([0]), false],
            [new MultiValue([1]), false],
            [new MultiValue(['0']), false],
            [new MultiValue(['1']), false],
            [new MultiValue(['value1']), false],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider valueIsEmptyProvider
     * @test
     */
    public function valueIsEmpty($value, $expected)
    {
        $result = GeneralUtility::isEmpty($value);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
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
            [new MultiValue([0]), true],
            [new MultiValue([1]), true],
            [new MultiValue([5]), true],
            [new MultiValue(['']), true],
            [new MultiValue(['value1']), true],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider valueIsTrueProvider
     * @test
     */
    public function valueIsTrue($value, $expected)
    {
        $result = GeneralUtility::isTrue($value);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param $value
     * @param $notExpected
     * @dataProvider valueIsTrueProvider
     * @test
     */
    public function valueIsFalse($value, $notExpected)
    {
        $result = GeneralUtility::isFalse($value);
        if ($notExpected) {
            $this->assertFalse($result);
        } else {
            $this->assertTrue($result);
        }
    }

    public function parseSeparatorStringProvider(): array
    {
        return [
            ['', ""],
            [' ', ""],
            [' value1 ', "value1"],
            ['\\s', " "],
            ['\\t', "\t"],
            ['\\n', "\n"],
            ['\\s\\t\\n\\t\\s', " \t\n\t "],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider parseSeparatorStringProvider
     * @test
     */
    public function parseSeparatorString($value, $expected)
    {
        $result = GeneralUtility::parseSeparatorString($value);
        $this->assertEquals($expected, $result);
    }

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
     * @param $value
     * @param $expected
     * @dataProvider isListProvider
     * @test
     */
    public function isList($value, $expected)
    {
        $result = GeneralUtility::isList($value);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

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
     * @param $value
     * @param $token
     * @param $trim
     * @param $expected
     * @dataProvider castValueToArrayProvider
     * @test
     */
    public function castValueToArray($value, $token, $trim, $expected)
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
     * @param $submission
     * @param $short
     * @param $expected
     * @dataProvider calculateHashProvider
     * @test
     */
    public function calculateHash($submission, $short, $expected)
    {
        $result = GeneralUtility::calculateHash($submission, $short);
        $this->assertEquals($expected, $result);
    }

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
     * @param $hash
     * @param $expected
     * @dataProvider shortenHashProvider
     * @test
     */
    public function shortenHash($hash, $expected)
    {
        $result = GeneralUtility::shortenHash($hash);
        $this->assertEquals($expected, $result);
    }

    public function compareValueProvider(): array
    {
        // values in one group are considered to be equal
        $valueGroups = [
            [null, '',],
            [0, '0',],
            [1, '1',],
            [5, '5',],
            ['value1',],
            ['value2']
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
     * @param $fieldValue
     * @param $compareValue
     * @param bool $expected
     * @dataProvider compareValueProvider
     * @test
     */
    public function compareValue($fieldValue, $compareValue, bool $expected)
    {
        $result = GeneralUtility::compareValue($fieldValue, $compareValue);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

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

    public function compareListsProvider(): array
    {
        // values in one group are considered to be equal
        $valueGroups = [
            [new MultiValue(), '',],
            [new MultiValue(['value1'])],
            [new MultiValue(['value2'])],
            [new MultiValue(['value1', 'value2']), new MultiValue(['value2', 'value1']), 'value1,value2'],
            [new MultiValue([5,7,13]), new MultiValue([13,7,5]), '5,7,13',],
        ];
        return $this->generateComparisonPairs($valueGroups);
    }

    /**
     * @param $fieldValue
     * @param $compareValue
     * @param bool $expected
     * @dataProvider compareListsProvider
     * @test
     */
    public function compareLists($fieldValue, $compareValue, bool $expected)
    {
        $result = GeneralUtility::compareLists($fieldValue, $compareValue);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param $fieldValue
     * @param $compareValue
     * @param bool $expected
     * @dataProvider compareValueProvider
     * @dataProvider compareListsProvider
     * @test
     */
    public function compare($fieldValue, $compareValue, bool $expected)
    {
        $result = GeneralUtility::compare($fieldValue, $compareValue);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function findInListProvider(): array
    {
        return [
            ['value1', ['value1', 'value2', 'value3'], 0],
            ['value2', ['value1', 'value2', 'value3'], 1],
            ['value4', ['value1', 'value2', 'value3'], false],
        ];
    }

    /**
     * @param $fieldValue
     * @param $list
     * @param $expected
     * @dataProvider findInListProvider
     * @test
     */
    public function findInList($fieldValue, $list, $expected)
    {
        $result = GeneralUtility::findInList($fieldValue, $list);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param $fieldValue
     * @param $list
     * @param $expected
     * @dataProvider findInListProvider
     * @test
     */
    public function isInList($fieldValue, $list, $expected)
    {
        $result = GeneralUtility::isInList($fieldValue, $list);
        if ($expected === false) {
            $this->assertFalse($result);
        } else {
            $this->assertTrue($result);
        }
    }

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
     * @param $class
     * @param $interface
     * @param $expected
     * @dataProvider getPluginKeywordProvider
     * @test
     */
    public function getPluginKeyword($class, $interface, $expected)
    {
        $result = GeneralUtility::getPluginKeyword($class, $interface);
        $this->assertEquals($expected, $result);
    }
}
