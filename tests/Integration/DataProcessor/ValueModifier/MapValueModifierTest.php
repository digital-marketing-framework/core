<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\MapValueModifierTest as MapValueModifierUnitTest;

/**
 * @covers MapValueModifier
 */
class MapValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'map';

    public function modifyProvider(): array
    {
        return MapValueModifierUnitTest::modifyTestCases();
    }
}
