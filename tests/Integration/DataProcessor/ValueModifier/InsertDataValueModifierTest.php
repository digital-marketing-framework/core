<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\InsertDataValueModifier;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier\InsertDataValueModifierTest as InsertDataValueModifierUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InsertDataValueModifier::class)]
class InsertDataValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'insertData';

    protected function setUp(): void
    {
        parent::setUp();
        $this->data['field1'] = 'value1';
        $this->data['field2'] = 'value2';
        $this->data['field3'] = 'value3';
        $this->data['multiValue'] = new MultiValue(['a', 'b', 'c']);
    }

    public static function modifyProvider(): array
    {
        return InsertDataValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
