<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\TrimValueModifier;

class TrimValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'trim';

    protected const CLASS_NAME = TrimValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [null,          null],
        ['',            ''],
        [' ',           ''],
        ["\t",          ''],
        ["\n",          ''],
        [' value1 ',    'value1'],
        ['val ue1',     'val ue1'],
        [' val ue1 ',   'val ue1'],
        ['value1',      'value1'],
        ["\t value1\n", 'value1'],

        [[], []],
        [['', ' ', ' value3 ', 'value4'], ['', '', 'value3', 'value4']],
        [[['', ' ', ' value3 ', 'value4']], [['', '', 'value3', 'value4']]],
    ];

    public function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
