<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapReferenceValueModifier;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

class MapReferenceValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'mapReference';

    protected const CLASS_NAME = MapReferenceValueModifier::class;

    protected const DEFAULT_CONFIG = [
        MapReferenceValueModifier::KEY_MAP_NAME => MapReferenceValueModifier::DEFAULT_MAP_NAME,
        MapReferenceValueModifier::KEY_INVERT => MapReferenceValueModifier::DEFAULT_INVERT,
    ];

    public const MODIFY_TEST_CASES = [
        [null, null],
        [null, null, [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m1', MapReferenceValueModifier::KEY_INVERT => false]],
        [null, null, [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m1', MapReferenceValueModifier::KEY_INVERT => true]],

        ['a1', 'a2', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m1', MapReferenceValueModifier::KEY_INVERT => false]],
        ['b1', 'b2', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m1', MapReferenceValueModifier::KEY_INVERT => false]],
        ['c1', 'c1', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m1', MapReferenceValueModifier::KEY_INVERT => false]],

        ['a1', 'a3', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m2', MapReferenceValueModifier::KEY_INVERT => false]],
        ['b1', 'b1', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m2', MapReferenceValueModifier::KEY_INVERT => false]],
        ['c1', 'c3', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m2', MapReferenceValueModifier::KEY_INVERT => false]],

        ['a2', 'a1', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m1', MapReferenceValueModifier::KEY_INVERT => true]],
        ['b2', 'b1', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m1', MapReferenceValueModifier::KEY_INVERT => true]],
        ['c2', 'c2', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m1', MapReferenceValueModifier::KEY_INVERT => true]],

        ['a3', 'a1', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m2', MapReferenceValueModifier::KEY_INVERT => true]],
        ['b3', 'b3', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m2', MapReferenceValueModifier::KEY_INVERT => true]],
        ['c3', 'c1', [MapReferenceValueModifier::KEY_MAP_NAME => 'id.m2', MapReferenceValueModifier::KEY_INVERT => true]],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $map1 = [
            'id.m1.1' => $this->createMapItem('a1', 'a2', 'id.m1.1', 10),
            'id.m1.2' => $this->createMapItem('b1', 'b2', 'id.m1.2', 20),
        ];
        $map2 = [
            'id.m2.1' => $this->createMapItem('a1', 'a3', 'id.m2.1', 10),
            'id.m2.2' => $this->createMapItem('c1', 'c3', 'id.m2.2', 20),
        ];
        $this->configuration[0][ConfigurationInterface::KEY_VALUE_MAPS] = [
            'id.m1' => $this->createMapItem('map1', $map1, 'id.m1', 10),
            'id.m2' => $this->createMapItem('map2', $map2, 'id.m2', 20),
        ];
    }

    public function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
