<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\MapValueModifierTest as MapValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MapValueModifier::class)]
class MapValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'map';

    public static function modifyProvider(): array
    {
        return MapValueModifierUnitTest::modifyTestCases();
    }
}
