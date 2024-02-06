<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\SwitchValueSource;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueSource\SwitchValueSource
 */
class SwitchValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'switch';

    /**
     * @return array<string,array{string,string,array<string,array{uuid:string,key:string,value:string,weight:int}>,bool,string}>
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
                false,
                '',
            ],
            'switchMatchNoDefault' => [
                'value1',
                null,
                [
                    'id1' => static::createMapItem('value2', 'value2b', 'id1'),
                ],
                false,
                '',
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
     * @param array<string,array{uuid:string,key:string,value:string,weight:int}> $cases
     *
     * @test
     *
     * @dataProvider switchDataProvider
     */
    public function switchValue(mixed $value, ?string $expectedResult, array $cases, bool $useDefault = false, string $default = ''): void
    {
        $this->data['field1'] = $value;
        $config = [
            SwitchValueSource::KEY_SWITCH => $this->getValueConfiguration([
                FieldValueSource::KEY_FIELD_NAME => 'field1',
            ], 'field'),
            SwitchValueSource::KEY_CASES => $cases,
            SwitchValueSource::KEY_USE_DEFAULT => $useDefault,
            SwitchValueSource::KEY_DEFAULT => $default,
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
