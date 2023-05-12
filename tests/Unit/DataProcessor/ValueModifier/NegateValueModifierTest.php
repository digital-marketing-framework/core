<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\NegateValueModifier;
use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValue;

class NegateValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'negate';
    protected const CLASS_NAME = NegateValueModifier::class;

    public static function modifyTestCases(): array
    {
        return [
            [null,          null],
            [null,          null, [NegateValueModifier::KEY_TRUE => '1', NegateValueModifier::KEY_FALSE => '0']],
            [null,          null, [NegateValueModifier::KEY_TRUE => 'a', NegateValueModifier::KEY_FALSE => 'b']],

            ['1',           '0'],
            ['1',           '0', [NegateValueModifier::KEY_TRUE => '1', NegateValueModifier::KEY_FALSE => '0']],
            
            ['',            '1'],
            ['',            '1', [NegateValueModifier::KEY_TRUE => '1', NegateValueModifier::KEY_FALSE => '0']],
            
            ['abc',         '0'],
            ['abc',         '0', [NegateValueModifier::KEY_TRUE => '1', NegateValueModifier::KEY_FALSE => '0']],
            
            ['a',           'b', [NegateValueModifier::KEY_TRUE => 'a', NegateValueModifier::KEY_FALSE => 'b']],
            ['a',           'b', [NegateValueModifier::KEY_TRUE => 'b', NegateValueModifier::KEY_FALSE => 'a']],

            ['xyz',         'b', [NegateValueModifier::KEY_TRUE => 'a', NegateValueModifier::KEY_FALSE => 'b']],
            ['xyz',         'a', [NegateValueModifier::KEY_TRUE => 'b', NegateValueModifier::KEY_FALSE => 'a']],

            ['',            '0', [NegateValueModifier::KEY_TRUE => '0', NegateValueModifier::KEY_FALSE => '1']],
            ['abc',         '1', [NegateValueModifier::KEY_TRUE => '0', NegateValueModifier::KEY_FALSE => '1']],

            [new BooleanValue(true), '0'],
            [new BooleanValue(true), '0', [NegateValueModifier::KEY_TRUE => '1', NegateValueModifier::KEY_FALSE => '0']],
            
            [new BooleanValue(false), '1'],
            [new BooleanValue(false), '1', [NegateValueModifier::KEY_TRUE => '1', NegateValueModifier::KEY_FALSE => '0']],

            [new BooleanValue(true, 'a', 'b'), 'b'],
            [new BooleanValue(true, 'a', 'b'), '0', [NegateValueModifier::KEY_TRUE => '1', NegateValueModifier::KEY_FALSE => '0']],
            [new BooleanValue(true, 'a', 'b'), 'y', [NegateValueModifier::KEY_TRUE => 'x', NegateValueModifier::KEY_FALSE => 'y']],

            [new BooleanValue(false, 'a', 'b'), 'a'],
            [new BooleanValue(false, 'a', 'b'), '1', [NegateValueModifier::KEY_TRUE => '1', NegateValueModifier::KEY_FALSE => '0']],
            [new BooleanValue(false, 'a', 'b'), 'x', [NegateValueModifier::KEY_TRUE => 'x', NegateValueModifier::KEY_FALSE => 'y']],
            
            [['1','0'], ['0', '1']],
            [['1','0'], ['0', '1'], [NegateValueModifier::KEY_TRUE => '1', NegateValueModifier::KEY_FALSE => '0']],
            [['1','0'], ['b', 'a'], [NegateValueModifier::KEY_TRUE => 'a', NegateValueModifier::KEY_FALSE => 'b']],
        ];
    }
    
    public function modifyProvider(): array
    {
        return static::modifyTestCases();
    }
}