<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\UpperCaseValueModifier;

class UpperCaseValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'upperCase';

    protected const CLASS_NAME = UpperCaseValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [null,     null],
        ['value1', 'VALUE1'],
        ['VALUE1', 'VALUE1'],
        ['1_2_3',  '1_2_3'],

        [[], []],
        [['Value1', 'VALUE2', 'value3'], ['VALUE1', 'VALUE2', 'VALUE3']],
    ];

    public function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
