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
        return MapReferenceValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
