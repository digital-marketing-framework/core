<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\SpliceValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\SpliceValueModifierTest as SpliceValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SpliceValueModifier::class)]
class SpliceValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'splice';

    public static function modifyProvider(): array
    {
        return SpliceValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
