<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\VariableValueSource;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(VariableValueSource::class)]
class VariableValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'variable';

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
    public function variableValue(string $reference, ?string $expectedResult): void
    {
        $output = $this->processValueSource($this->getValueSourceConfiguration([
            VariableValueSource::KEY_VARIABLE_NAME => $reference,
        ]));

        if ($expectedResult === null) {
            $this->assertNull($output);
        } else {
            $this->assertIsString($output);
            $this->assertEquals($expectedResult, $output);
        }
    }
}
