<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\DateModifyValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\DateModifyValueModifierTest as DateModifyValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DateModifyValueModifier::class)]
class DateModifyValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'dateModify';

    public static function modifyProvider(): array
    {
        return DateModifyValueModifierUnitTest::modifyProvider();
    }
}
