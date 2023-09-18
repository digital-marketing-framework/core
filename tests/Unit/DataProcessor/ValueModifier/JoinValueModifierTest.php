<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\JoinValueModifier;

class JoinValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'join';

    protected const CLASS_NAME = JoinValueModifier::class;

    protected const DEFAULT_CONFIG = [
        JoinValueModifier::KEY_GLUE => JoinValueModifier::DEFAULT_GLUE,
    ];

    public const MODIFY_TEST_CASES = [
        [null, null],
        ['', ''],
        ['value1', 'value1'],
        [[], ''],
        [['a', 'b', 'c'], 'a,b,c'],
        [['a', 'b', 'c'], 'abc', [JoinValueModifier::KEY_GLUE => '']],
        [['a', 'b', 'c'], 'a;b;c', [JoinValueModifier::KEY_GLUE => ';']],
        [['a', ['ba', 'bb'], 'c'], 'a,ba,bb,c'],
        [['a', ['ba', 'bb'], 'c'], 'a;ba,bb;c', [JoinValueModifier::KEY_GLUE => ';']],
    ];

    public function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
