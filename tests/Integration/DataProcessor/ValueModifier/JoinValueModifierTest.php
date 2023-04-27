<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\JoinValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\JoinValueModifierTest as JoinValueModifierUnitTest;

/**
 * @covers JoinValueModifier
 */
class JoinValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'join';
    
    public function modifyProvider(): array
    {
        return JoinValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
