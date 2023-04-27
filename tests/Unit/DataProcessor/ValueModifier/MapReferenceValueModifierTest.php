<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapReferenceValueModifier;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;

class MapReferenceValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'mapReference';
    protected const CLASS_NAME = MapReferenceValueModifier::class;

    public const MODIFY_TEST_CASES = [
        [null, null],
        [null, null, [MapReferenceValueModifier::KEY_MAP_NAME => 'map1', MapReferenceValueModifier::KEY_INVERT => false]],
        [null, null, [MapReferenceValueModifier::KEY_MAP_NAME => 'map1', MapReferenceValueModifier::KEY_INVERT => true]],

        ['a1', 'a2', [MapReferenceValueModifier::KEY_MAP_NAME => 'map1', MapReferenceValueModifier::KEY_INVERT => false]],
        ['b1', 'b2', [MapReferenceValueModifier::KEY_MAP_NAME => 'map1', MapReferenceValueModifier::KEY_INVERT => false]],
        ['c1', 'c1', [MapReferenceValueModifier::KEY_MAP_NAME => 'map1', MapReferenceValueModifier::KEY_INVERT => false]],

        ['a1', 'a3', [MapReferenceValueModifier::KEY_MAP_NAME => 'map2', MapReferenceValueModifier::KEY_INVERT => false]],
        ['b1', 'b1', [MapReferenceValueModifier::KEY_MAP_NAME => 'map2', MapReferenceValueModifier::KEY_INVERT => false]],
        ['c1', 'c3', [MapReferenceValueModifier::KEY_MAP_NAME => 'map2', MapReferenceValueModifier::KEY_INVERT => false]],

        ['a2', 'a1', [MapReferenceValueModifier::KEY_MAP_NAME => 'map1', MapReferenceValueModifier::KEY_INVERT => true]],
        ['b2', 'b1', [MapReferenceValueModifier::KEY_MAP_NAME => 'map1', MapReferenceValueModifier::KEY_INVERT => true]],
        ['c2', 'c2', [MapReferenceValueModifier::KEY_MAP_NAME => 'map1', MapReferenceValueModifier::KEY_INVERT => true]],

        ['a3', 'a1', [MapReferenceValueModifier::KEY_MAP_NAME => 'map2', MapReferenceValueModifier::KEY_INVERT => true]],
        ['b3', 'b3', [MapReferenceValueModifier::KEY_MAP_NAME => 'map2', MapReferenceValueModifier::KEY_INVERT => true]],
        ['c3', 'c1', [MapReferenceValueModifier::KEY_MAP_NAME => 'map2', MapReferenceValueModifier::KEY_INVERT => true]],
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->configuration[0][ConfigurationInterface::KEY_VALUE_MAPS]['map1'] = [
            'a1' => 'a2',
            'b1' => 'b2',
        ];
        $this->configuration[0][ConfigurationInterface::KEY_VALUE_MAPS]['map2'] = [
            'a1' => 'a3',
            'c1' => 'c3',
        ];
    }
    
    public function modifyProvider(): array
    {
        return static::MODIFY_TEST_CASES;
    }
}
