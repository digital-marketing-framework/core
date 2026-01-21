<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\DateFormatValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\DateFormatValueModifierTest as DateFormatValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DateFormatValueModifier::class)]
class DateFormatValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'dateFormat';

    public static function modifyProvider(): array
    {
        return DateFormatValueModifierUnitTest::modifyProvider();
    }
}
