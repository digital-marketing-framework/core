<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\LowerCaseValueModifier;

class LowerCaseValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'lowerCase';

    protected const CLASS_NAME = LowerCaseValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [null,     null],
        ['VALUE1', 'value1'],
        ['value1', 'value1'],
        ['1_2_3',  '1_2_3'],

        [[], []],
        [['Value1', 'VALUE2', 'value3'], ['value1', 'value2', 'value3']],
    ];

    public function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
