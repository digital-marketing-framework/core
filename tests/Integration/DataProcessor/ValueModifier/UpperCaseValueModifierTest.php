<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\UpperCaseValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\UpperCaseValueModifierTest as UpperCaseValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpperCaseValueModifier::class)]
class UpperCaseValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'upperCase';

    public static function modifyProvider(): array
    {
        return UpperCaseValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
