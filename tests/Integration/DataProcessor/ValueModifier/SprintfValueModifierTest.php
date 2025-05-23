<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\SprintfValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\SprintfValueModifierTest as SprintfValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SprintfValueModifier::class)]
class SprintfValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'sprintf';

    public static function modifyProvider(): array
    {
        return SprintfValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
