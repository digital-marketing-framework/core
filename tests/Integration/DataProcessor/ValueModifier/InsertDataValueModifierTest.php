<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\InsertDataValueModifier;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
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
        $this->data['field.with.dots'] = 'dotted';
        $this->data['field with spaces'] = 'spaced';
        $this->data['collision'] = 'fieldValue';

        $this->configuration[0][ConfigurationInterface::KEY_DATA_PROCESSING][ConfigurationInterface::KEY_VARIABLES] = [
            'id.v1' => $this->createMapItem('campaignCode', 'PPC-EN-JUNE19', 'id.v1', 10),
            'id.v2' => $this->createMapItem('emptyVariable', '', 'id.v2', 20),
            'id.v3' => $this->createMapItem('collision', 'variableValue', 'id.v3', 30),
        ];
    }

    public static function modifyProvider(): array
    {
        return InsertDataValueModifierUnitTest::MODIFY_TEST_CASES;
    }
}
