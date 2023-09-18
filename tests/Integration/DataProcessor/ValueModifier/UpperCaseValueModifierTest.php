<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\UpperCaseValueModifierTest as UpperCaseValueModifierUnitTest;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueModifier\UpperCaseValueModifier
 */
class UpperCaseValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'upperCase';

    public function modifyProvider(): array
    {
        return UpperCaseValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
