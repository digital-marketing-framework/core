<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\MapValueModifierTest as MapValueModifierUnitTest;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapValueModifier
 */
class MapValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'map';

    public function modifyProvider(): array
    {
        return MapValueModifierUnitTest::modifyTestCases();
    }
}
