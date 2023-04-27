<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\SprintfValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\SprintfValueModifierTest as SprintfValueModifierUnitTest;

/**
 * @covers SprintfValueModifier
 */
class SprintfValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'sprintf';
    
    public function modifyProvider(): array
    {
        return SprintfValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
