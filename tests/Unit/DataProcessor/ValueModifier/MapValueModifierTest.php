<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapValueModifier;

class MapValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'map';

    protected const CLASS_NAME = MapValueModifier::class;

    protected const DEFAULT_CONFIG = [
        MapValueModifier::KEY_MAP => MapValueModifier::DEFAULT_MAP,
    ];

    /**
     * @return array<array{0:mixed,1:mixed,2:?array<string,mixed>}>
     */
    public static function modifyTestCases(): array
    {
        return [
            [null, null],
            [null, null, ['a1' => 'a2', 'b1' => 'b2']],

            ['c1', 'c1'],
            ['c1', 'c1', [
                MapValueModifier::KEY_MAP => [
                    static::createMapItem('a1', 'a2', 'id1', 10),
                    static::createMapItem('b1', 'b2', 'id2', 20),
                ],
            ]],

            ['a1', 'a1'],
            ['a1', 'a2', [
                MapValueModifier::KEY_MAP => [
                    static::createMapItem('a1', 'a2', 'id1', 10),
                    static::createMapItem('b1', 'b2', 'id2', 20),
                ],
            ]],

            [['a1', 'c1', 'b1'], ['a2', 'c1', 'b2'], [
                MapValueModifier::KEY_MAP => [
                    static::createMapItem('a1', 'a2', 'id1', 10),
                    static::createMapItem('b1', 'b2', 'id2', 20),
                ],
            ]],

            [[['a1', 'c1', 'b1']], [['a2', 'c1', 'b2']], [
                MapValueModifier::KEY_MAP => [
                    static::createMapItem('a1', 'a2', 'id1', 10),
                    static::createMapItem('b1', 'b2', 'id2', 20),
                ],
            ]],
        ];
    }

    public function modifyProvider(): array
    {
        return static::modifyTestCases();
    }
}
