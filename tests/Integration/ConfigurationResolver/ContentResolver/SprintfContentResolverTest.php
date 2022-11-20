<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\SprintfContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

/**
 * @covers SprintfContentResolver
 */
class SprintfContentResolverTest extends AbstractContentResolverTest
{
    public function sprintfProvider(): array
    {
        return [
            // value, format, expected
            [null, 'format1', null],
            [null, '', null],

            ['value1', '', 'value1'],
            ['value1', 'format1', 'format1'],
            ['value1', '%s', 'value1'],
            ['value1', 'format1:%s', 'format1:value1'],

            ['1.2', '%01.2f', '1.20'],
            [1.2, '%01.2f', '1.20'],
            [34.567, '%01.2f', '34.57'],

            [new MultiValue(['value1', 'value2']), 'format1', 'format1'],
            [new MultiValue(['value1', 'value2']), '%s', 'value1'],
            [new MultiValue(['value1', 'value2']), '%s:%s', 'value1:value2'],
            [new MultiValue([1.2, 34.567]), '%01.2f - %01.2f', '1.20 - 34.57'],
        ];
    }

    /**
     * @dataProvider sprintfProvider
     * @test
     */
    public function sprintf(mixed $value, string $format, mixed $expected): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => $value,
            'sprintf' => $format,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals($expected, $result);
    }
}
