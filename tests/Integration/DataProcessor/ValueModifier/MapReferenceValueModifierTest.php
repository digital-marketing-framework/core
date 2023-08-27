<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapReferenceValueModifier;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\MapReferenceValueModifierTest as MapReferenceValueModifierUnitTest;

/**
 * @covers MapReferenceValueModifier
 */
class MapReferenceValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'mapReference';

    public function setUp(): void
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
            'id.m1' => $this->createMapItem('map1', $map1, 'id.m1', 10,),
            'id.m2' => $this->createMapItem('map2', $map2, 'id.m2', 20,),
        ];
    }

    public function modifyProvider(): array
    {
        return MapReferenceValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
