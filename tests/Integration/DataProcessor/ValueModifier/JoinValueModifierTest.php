<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\JoinValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\JoinValueModifierTest as JoinValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(JoinValueModifier::class)]
class JoinValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'join';

    public static function modifyProvider(): array
    {
        return JoinValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
