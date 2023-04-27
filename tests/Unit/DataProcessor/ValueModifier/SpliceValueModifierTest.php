<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\SpliceValueModifier;

class SpliceValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'splice';
    protected const CLASS_NAME = SpliceValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [null,          null],
        ["",            ""],
        ['first second third', 'first', [SpliceValueModifier::KEY_INDEX => '1']],
        ['first second third', 'second', [SpliceValueModifier::KEY_INDEX => '2']],
        ['first second third', '', [SpliceValueModifier::KEY_INDEX => '4']],
        ['first second third', 'third', [SpliceValueModifier::KEY_INDEX => '-1']],
        ['first second third', 'first', [SpliceValueModifier::KEY_INDEX => '-3']],
        ['first second third', 'first', [SpliceValueModifier::KEY_INDEX => '-4']],
        ['first-second-third', 'second', [SpliceValueModifier::KEY_INDEX => '2', SpliceValueModifier::KEY_TOKEN => '-']],

        [null, null, [SpliceValueModifier::KEY_INDEX => '1:']],
        ['', '', [SpliceValueModifier::KEY_INDEX => '1:']],
        ['first second third', 'first second third', [SpliceValueModifier::KEY_INDEX => '1:']],
        ['first second third', 'second third', [SpliceValueModifier::KEY_INDEX => '2:']],
        ['first second third', 'third', [SpliceValueModifier::KEY_INDEX => '3:']],
        ['first second third', '', [SpliceValueModifier::KEY_INDEX => '4:']],
        ['first second third', 'third', [SpliceValueModifier::KEY_INDEX => '-1:']],
        ['first second third', 'second third', [SpliceValueModifier::KEY_INDEX => '-2:']],
        ['first second third', 'first second third', [SpliceValueModifier::KEY_INDEX => '-3:']],
        ['first second third', 'first second third', [SpliceValueModifier::KEY_INDEX => '-4:']],

        ['first second third', 'first', [SpliceValueModifier::KEY_INDEX => '1:1']],
        ['first second third', 'first second', [SpliceValueModifier::KEY_INDEX => '1:2']],
        ['first second third', 'second', [SpliceValueModifier::KEY_INDEX => '2:1']],
        ['first second third', 'second third', [SpliceValueModifier::KEY_INDEX => '2:2']],
        ['first second third', 'first second', [SpliceValueModifier::KEY_INDEX => '1:-1']],
        ['first second third', 'first', [SpliceValueModifier::KEY_INDEX => '1:-2']],

        ['first second third', 'first', [SpliceValueModifier::KEY_INDEX => ':1']],
        ['first second third', 'first second', [SpliceValueModifier::KEY_INDEX => ':2']],
        ['first second third', 'first second third', [SpliceValueModifier::KEY_INDEX => ':4']],
        ['first second third', 'first second', [SpliceValueModifier::KEY_INDEX => ':-1']],
        ['first second third', 'first', [SpliceValueModifier::KEY_INDEX => ':-2']],
    ];
    
    public function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
