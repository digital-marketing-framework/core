<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\VariableValueSource;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends ValueSourceTestBase<VariableValueSource>
 */
class VariableValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'variable';

    protected const CLASS_NAME = VariableValueSource::class;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configuration[0][ConfigurationInterface::KEY_DATA_PROCESSING][ConfigurationInterface::KEY_VARIABLES] = [
            'id.v1' => $this->createMapItem('campaignCode', 'PPC-EN-JUNE19', 'id.v1', 10),
            'id.v2' => $this->createMapItem('emptyVariable', '', 'id.v2', 20),
        ];
    }

    /**
     * @return array<string,array{string,?string}>
     */
    public static function variableDataProvider(): array
    {
        return [
            'declaredVariable' => ['id.v1', 'PPC-EN-JUNE19'],
            'declaredButEmptyVariable' => ['id.v2', ''],
            'undeclaredVariable' => ['id.unknown', null],
            'noReferenceSelected' => ['', null],
        ];
    }

    #[Test]
    #[DataProvider('variableDataProvider')]
    public function variable(string $reference, ?string $expected): void
    {
        $result = $this->processValueSource([
            VariableValueSource::KEY_VARIABLE_NAME => $reference,
        ]);

        $this->assertSame($expected, $result);
    }

    /**
     * The value source addresses a variable by UUID, insertData by key. Both must find
     * the same variable.
     */
    #[Test]
    public function theSameVariableIsReachableByUuidAndByKey(): void
    {
        $configuration = $this->getContext()->getConfiguration();

        $this->assertSame('PPC-EN-JUNE19', $configuration->getVariableConfiguration('id.v1'));
        $this->assertSame('PPC-EN-JUNE19', $configuration->getVariableConfigurationByKey('campaignCode'));
        $this->assertNull($configuration->getVariableConfigurationByKey('id.v1'));
        $this->assertNull($configuration->getVariableConfiguration('campaignCode'));
    }

    #[Test]
    public function variableIsOverriddenByChildDocument(): void
    {
        $this->configuration[] = [
            ConfigurationInterface::KEY_DATA_PROCESSING => [
                ConfigurationInterface::KEY_VARIABLES => [
                    'id.v1' => ['value' => 'PPC-EN-MAY22'],
                ],
            ],
        ];

        $result = $this->processValueSource([
            VariableValueSource::KEY_VARIABLE_NAME => 'id.v1',
        ]);

        $this->assertSame('PPC-EN-MAY22', $result);
    }
}
