<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\FilterEmptyValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\FilterEmptyValueModifierTest as FilterEmptyValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FilterEmptyValueModifier::class)]
class FilterEmptyValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'filterEmpty';

    public static function modifyProvider(): array
    {
        return FilterEmptyValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
