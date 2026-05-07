<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\SwitchReferenceValueSource;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(SwitchReferenceValueSource::class)]
class SwitchReferenceValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'switchReference';

    protected function setUp(): void
    {
        parent::setUp();
        $map1 = [
            'id.m1.1' => $this->createMapItem('a1', 'a2', 'id.m1.1', 10),
            'id.m1.2' => $this->createMapItem('b1', 'b2', 'id.m1.2', 20),
        ];
        $this->configuration[0][ConfigurationInterface::KEY_DATA_PROCESSING][ConfigurationInterface::KEY_VALUE_MAPS] = [
            'id.m1' => $this->createMapItem('map1', $map1, 'id.m1', 10),
        ];
    }

    /**
     * @return array<string,array{?string,?string,string,bool,bool,string}>
     */
    public static function switchReferenceDataProvider(): array
    {
        return [
            'originalValueIsNullNoDefault' => [
                null,
                null,
                'id.m1',
                false,
                false,
                '',
            ],
            'originalValueIsNullWithDefault' => [
                null,
                'fallback',
                'id.m1',
                false,
                true,
                'fallback',
            ],
            'switchMatch' => [
                'a1',
                'a2',
                'id.m1',
                false,
                false,
                '',
            ],
            'switchMissNoDefault' => [
                'c1',
                null,
                'id.m1',
                false,
                false,
                '',
            ],
            'switchMissDefault' => [
                'c1',
                'fallback',
                'id.m1',
                false,
                true,
                'fallback',
            ],
            'switchMatchDefaultIgnored' => [
                'a1',
                'a2',
                'id.m1',
                false,
                true,
                'fallback',
            ],
            'unknownMapNoDefault' => [
                'a1',
                null,
                'id.unknown',
                false,
                false,
                '',
            ],
            'unknownMapWithDefault' => [
                'a1',
                'fallback',
                'id.unknown',
                false,
                true,
                'fallback',
            ],
            'invertedMatch' => [
                'a2',
                'a1',
                'id.m1',
                true,
                false,
                '',
            ],
            'invertedMissDefault' => [
                'a1',
                'fallback',
                'id.m1',
                true,
                true,
                'fallback',
            ],
        ];
    }

    #[Test]
    #[DataProvider('switchReferenceDataProvider')]
    public function switchReferenceValue(?string $value, ?string $expectedResult, string $mapName, bool $invert, bool $useDefault, string $default): void
    {
        $this->data['field1'] = $value;
        $config = [
            SwitchReferenceValueSource::KEY_SWITCH => $this->getValueConfiguration([
                FieldValueSource::KEY_FIELD_NAME => 'field1',
            ], 'field'),
            SwitchReferenceValueSource::KEY_MAP_NAME => $mapName,
            SwitchReferenceValueSource::KEY_INVERT => $invert,
            SwitchReferenceValueSource::KEY_USE_DEFAULT => $useDefault,
            SwitchReferenceValueSource::KEY_DEFAULT => $default,
        ];

        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        if ($expectedResult === null) {
            $this->assertNull($output);
        } else {
            $this->assertIsString($output);
            $this->assertEquals($expectedResult, $output);
        }
    }
}
