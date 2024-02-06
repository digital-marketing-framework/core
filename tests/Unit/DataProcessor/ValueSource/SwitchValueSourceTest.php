<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\SwitchValueSource;

/**
 * @extends ValueSourceTest<SwitchValueSource>
 */
class SwitchValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'switch';

    protected const CLASS_NAME = SwitchValueSource::class;

    /**
     * @return array<array<mixed>>
     */
    public function switchDataProvider(): array
    {
        return [
            'switchMatch' => [
                'value2',
                'value2b',
                [
                    'id1' => static::createMapItem('value1', 'value1b', 'id1', 10),
                    'id2' => static::createMapItem('value2', 'value2b', 'id2', 20),
                    'id3' => static::createMapItem('value3', 'value3b', 'id3', 30),
                ],
            ],
            'switchMatchNoDefault' => [
                'value1',
                null,
                [
                    'id1' => static::createMapItem('value2', 'value2b', 'id1'),
                ],
            ],
            'switchMatchDefault' => [
                'value1',
                'value1c',
                [
                    'id1' => static::createMapItem('value2', 'value2b', 'id1'),
                ],
                true,
                'value1c',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider switchDataProvider
     */
    public function switchValue(mixed $value, ?string $expectedResult, array $cases, bool $useDefault = false, string $default = ''): void
    {
        $subConfig = ['subConfigKey' => 'subConfigValue'];
        $this->dataProcessor->method('processValue')->with($subConfig)->willReturn($value);
        $config = [
            SwitchValueSource::KEY_SWITCH => $subConfig,
            SwitchValueSource::KEY_CASES => $cases,
            SwitchValueSource::KEY_USE_DEFAULT => $useDefault,
            SwitchValueSource::KEY_DEFAULT => $default,
        ];

        $output = $this->processValueSource($config);
        if ($expectedResult === null) {
            $this->assertNull($output);
        } else {
            $this->assertIsString($output);
            $this->assertEquals($expectedResult, $output);
        }
    }
}
