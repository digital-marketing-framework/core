<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\SplitContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

/**
 * @covers SplitContentResolver
 */
class SplitContentResolverTest extends AbstractContentResolverTest
{
    public function splitByIndexProvider(): array
    {
        return [
            // value, index, token, expected
            [null,                 null, null, null],
            ['',                   null, null, ''],
            ['first second third', null, null, 'first'],
            ['first second third', '1',  null, 'first'],
            ['first second third', '2',  null, 'second'],
            ['first second third', '4',  null, ''],
            ['first second third', '-1', null, 'third'],
            ['first second third', '-3', null, 'first'],
            ['first second third', '-4', null, 'first'],
            ['first-second-third', '2',  '-',  'second'],
            ['first second third', '1',  '-',  'first second third'],
            ['first second third', '2',  '-',  ''],
        ];
    }

    public function splitBySpliceProvider(): array
    {
        return [
            // value, splice, token, expected
            [null,                 '1:',   null, null],
            ['',                   '1:',   null, ''],
            ['first second third', '1:',   null, 'first second third'],
            ['first second third', '2:',   null, 'second third'],
            ['first second third', '3:',   null, 'third'],
            ['first second third', '4:',   null, ''],
            ['first second third', '-1:',  null, 'third'],
            ['first second third', '-2:',  null, 'second third'],
            ['first second third', '-3:',  null, 'first second third'],
            ['first second third', '-4:',  null, 'first second third'],
            ['first second third', '1:1',  null, 'first'],
            ['first second third', '1:2',  null, 'first second'],
            ['first second third', '2:1',  null, 'second'],
            ['first second third', '2:2',  null, 'second third'],
            ['first second third', '1:-1', null, 'first second'],
            ['first second third', '1:-2', null, 'first'],
            ['first second third', ':1',   null, 'first'],
            ['first second third', ':2',   null, 'first second'],
            ['first second third', ':4',   null, 'first second third'],
            ['first second third', ':-1',  null, 'first second'],
            ['first second third', ':-2',  null, 'first'],
        ];
    }

    protected function runSplit(mixed $value, mixed $index, mixed $token, mixed $expected, bool $direct, string $pointer): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => $value,
        ];
        if ($direct) {
            $config['split'] = $index !== null ? $index : true;
        } else {
            $config['split'] = [];
            if ($index !== null) {
                $config['split'][$pointer] = $index;
            }
            if ($token !== null) {
                $config['split']['token'] = $token;
            }
        }
        $result = $this->runResolverProcess($config);
        $this->assertEquals($expected, $result);
    }


    /**
     * @dataProvider splitBySpliceProvider
     * @test
     */
    public function splitByIndex(mixed $value, mixed $index, mixed $token, mixed $expected): void
    {
        $this->runSplit($value, $index, $token, $expected, false, 'index');
        if ($token === null) {
            $this->runSplit($value, $index, $token, $expected, true, 'index');
        }
    }


    /**
     * @dataProvider splitByIndexProvider
     * @test
     */
    public function splitBySplice(mixed $value, mixed $splice, mixed $token, mixed $expected): void
    {
        $this->runSplit($value, $splice, $token, $expected, false, 'slice');
        if ($token === null) {
            $this->runSplit($value, $splice, $token, $expected, true, 'slice');
        }
    }
}
