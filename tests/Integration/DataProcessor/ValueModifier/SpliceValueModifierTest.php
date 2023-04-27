<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\SpliceValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\SpliceValueModifierTest as SpliceValueModifierUnitTest;

/**
 * @covers SpliceValueModifier
 */
class SpliceValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'splice';
    
    public function modifyProvider(): array
    {
        return SpliceValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
