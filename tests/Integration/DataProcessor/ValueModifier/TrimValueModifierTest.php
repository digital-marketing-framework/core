<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\TrimValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\TrimValueModifierTest as TrimValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TrimValueModifier::class)]
class TrimValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'trim';

    public static function modifyProvider(): array
    {
        return TrimValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
