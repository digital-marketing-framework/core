<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\LowerCaseValueModifierTest as LowerCaseValueModifierUnitTest;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueModifier\LowerCaseValueModifier
 */
class LowerCaseValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'lowerCase';

    public function modifyProvider(): array
    {
        return LowerCaseValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
