<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\DefaultValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\DefaultValueModifierTest as DefaultValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DefaultValueModifier::class)]
class DefaultValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'default';

    public static function modifyProvider(): array
    {
        return DefaultValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
