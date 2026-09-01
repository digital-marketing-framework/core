<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\FilterEmptyValueModifier;

class FilterEmptyValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'filterEmpty';

    protected const CLASS_NAME = FilterEmptyValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [null,     null],
        ['',       null],
        [' ',      ' '],
        ['0',      '0'],
        ['value1', 'value1'],

        [[],                     []],
        [['value1', 'value2'],   ['value1', 'value2']],
        [['value1', '', 'value3'], ['value1', 'value3']],
        [['', 'value2'],         ['value2']],
        [['', ''],               []],
        [['value1', '0', ''],    ['value1', '0']],

        [['key1' => 'value1', 'key2' => '', 'key3' => 'value3'], ['key1' => 'value1', 'key3' => 'value3']],
        [['key1' => 'value1', '', 'value3'], ['key1' => 'value1', 'value3']],

        [[['value1.1', '', 'value1.3'], '', 'value3'], [['value1.1', 'value1.3'], 'value3']],
        [[['', ''], 'value2'], [[], 'value2']],
    ];

    public static function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
