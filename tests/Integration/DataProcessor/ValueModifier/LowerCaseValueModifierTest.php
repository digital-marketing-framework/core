<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\LowerCaseValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\LowerCaseValueModifierTest as LowerCaseValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LowerCaseValueModifier::class)]
class LowerCaseValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'lowerCase';

    public static function modifyProvider(): array
    {
        return LowerCaseValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
