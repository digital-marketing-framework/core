<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\DefaultValueModifier;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\DefaultValueModifierTest as DefaultValueModifierUnitTest;

/**
 * @covers DefaultValueModifier
 */
class DefaultValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'default';

    public function modifyProvider(): array
    {
        return DefaultValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
