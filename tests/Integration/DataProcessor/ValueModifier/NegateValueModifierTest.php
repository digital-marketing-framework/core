<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\NegateValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\NegateValueModifierTest as NegateValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(NegateValueModifier::class)]
class NegateValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'negate';

    public static function modifyProvider(): array
    {
        return NegateValueModifierUnitTest::modifyTestCases();
    }
}
