<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapValueModifier;

class MapValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'map';
    protected const CLASS_NAME = MapValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [null, null],
        [null, null, [MapValueModifier::KEY_MAP => ['a1' => 'a2', 'b1' => 'b2']]],

        ['c1', 'c1'],
        ['c1', 'c1', [MapValueModifier::KEY_MAP => ['a1' => 'a2', 'b1' => 'b2']]],

        ['a1', 'a1'],
        ['a1', 'a2', [MapValueModifier::KEY_MAP => ['a1' => 'a2', 'b1' => 'b2']]],

        [['a1','c1','b1'], ['a2','c1','b2'], [MapValueModifier::KEY_MAP => ['a1' => 'a2', 'b1' => 'b2']]],
        [[['a1','c1','b1']], [['a2','c1','b2']], [MapValueModifier::KEY_MAP => ['a1' => 'a2', 'b1' => 'b2']]],
    ];
    
    public function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
