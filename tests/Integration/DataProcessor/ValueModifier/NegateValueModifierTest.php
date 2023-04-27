<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\NegateValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\NegateValueModifierTest as NegateValueModifierUnitTest;

/**
 * @covers NegateValueModifier
 */
class NegateValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'negate';

    public function modifyProvider(): array
    {
        return NegateValueModifierUnitTest::modifyTestCases();
    }
}
