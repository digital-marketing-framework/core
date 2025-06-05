<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\DefaultValueModifier;

class DefaultValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'default';

    protected const CLASS_NAME = DefaultValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [null,      'default_value_x', [DefaultValueModifier::KEY_VALUE => 'default_value_x']],
        ['',        'default_value_x', [DefaultValueModifier::KEY_VALUE => 'default_value_x']],
        ['value_y', 'value_y',         [DefaultValueModifier::KEY_VALUE => 'default_value_x']],

        [[],          'default_value_x', [DefaultValueModifier::KEY_VALUE => 'default_value_x']],
        [['value_y'], ['value_y'],       [DefaultValueModifier::KEY_VALUE => 'default_value_x']],
    ];

    public static function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
