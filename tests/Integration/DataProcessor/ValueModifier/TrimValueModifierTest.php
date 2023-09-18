<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\TrimValueModifierTest as TrimValueModifierUnitTest;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueModifier\TrimValueModifier
 */
class TrimValueModifierTest extends ValueModifierTest
{
    protected const KEYWORD = 'trim';

    public function modifyProvider(): array
    {
        return TrimValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
