<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\DefaultValueModifierTest as DefaultValueModifierUnitTest;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueModifier\DefaultValueModifier
 */
class DefaultValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'default';

    public function modifyProvider(): array
    {
        return DefaultValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
