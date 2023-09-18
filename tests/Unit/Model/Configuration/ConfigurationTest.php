<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Configuration;

use BadMethodCallException;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    protected ConfigurationInterface $subject;

    /**
     * @return array<array{0:array<string,mixed>}>
     */
    public function toArrayProvider(): array
    {
        return [
            [[]],
            [['field1' => 'value1', 'field2' => 'value2']],
        ];
    }

    /**
     * @param array<string,mixed> $values
     *
     * @dataProvider toArrayProvider
     *
     * @test
     */
    public function toArray(array $values): void
    {
        $this->subject = new Configuration($values);
        $result = $this->subject->toArray();
        $this->assertEquals($values, $result);
    }

    /** @test */
    public function nonExistentBasicKey(): void
    {
        $this->subject = new Configuration([]);
        $this->assertNull($this->subject->get('key'));
        $this->assertEquals('default1', $this->subject->get('key', 'default1'));
    }

    /** @test */
    public function basicKeys(): void
    {
        $configList = [
            [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
        ];
        $this->subject = new Configuration($configList);
        $this->assertEquals('value1', $this->subject->get('key1'));
        $this->assertEquals('value2', $this->subject->get('key2'));
    }

    /** @test */
    public function basicKeysOverride(): void
    {
        $configList = [
            [
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3',
                'key4' => 'value4',
            ],
            [
                'key2' => 'value2b',
                'key3' => 'value3b',
                'key5' => 'value5b',
                'key6' => 'value6b',
            ],
            [
                'key3' => 'value3c',
                'key4' => 'value4c',
                'key6' => 'value6c',
                'key7' => 'value7c',
            ],
        ];
        $this->subject = new Configuration($configList);
        $this->assertEquals('value1', $this->subject->get('key1'));
        $this->assertEquals('value2b', $this->subject->get('key2'));
        $this->assertEquals('value3c', $this->subject->get('key3'));
        $this->assertEquals('value4c', $this->subject->get('key4'));
        $this->assertEquals('value5b', $this->subject->get('key5'));
        $this->assertEquals('value6c', $this->subject->get('key6'));
        $this->assertEquals('value7c', $this->subject->get('key7'));
    }

    /** @test */
    public function basicKeyDelete(): void
    {
        $configList = [
            ['key1' => 'value1'],
            ['key1' => null],
        ];
        $this->subject = new Configuration($configList);
        $this->assertNull($this->subject->get('key1'));
        $this->assertEquals('default1', $this->subject->get('key1', 'default1'));
    }

    /** @test */
    public function basicKeyDeletesArray(): void
    {
        $configList = [
            ['key1' => ['key1.1' => 'value1']],
            ['key1' => null],
        ];
        $this->subject = new Configuration($configList);
        $this->assertNull($this->subject->get('key1'));
        $this->assertEquals('default1', $this->subject->get('key1', 'default1'));
    }

    /** @test */
    public function dynamicallyAddedConfiguration(): void
    {
        $configList = [
            [
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3',
            ],
        ];
        $this->subject = new Configuration($configList, false);
        $this->subject->addConfiguration([
            'key2' => 'value2b',
            'key3' => null,
            'key4' => 'value4b',
        ]);
        $this->assertEquals('value1', $this->subject->get('key1'));
        $this->assertEquals('value2b', $this->subject->get('key2'));
        $this->assertNull($this->subject->get('key3'));
        $this->assertEquals('value4b', $this->subject->get('key4'));
        $this->assertNull($this->subject->get('key5'));
    }

    /** @test */
    public function manualOverride(): void
    {
        $configList = [
            [
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3',
            ],
        ];
        $this->subject = new Configuration($configList, false);
        $this->subject->set('key2', 'value2b');
        $this->subject->unset('key3');
        $this->subject->set('key4', 'value4b');

        $this->assertEquals('value1', $this->subject->get('key1'));
        $this->assertEquals('value2b', $this->subject->get('key2'));
        $this->assertNull($this->subject->get('key3'));
        $this->assertEquals('value4b', $this->subject->get('key4'));
    }

    /** @test */
    public function asArrayGet(): void
    {
        $configList = [
            ['key1' => 'value1'],
        ];
        $this->subject = new Configuration($configList);
        $this->assertEquals('value1', $this->subject['key1']);
        $this->assertNull($this->subject['key2']);
    }

    /** @test */
    public function asArraySet(): void
    {
        $configList = [
            [
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3',
            ],
        ];
        $this->subject = new Configuration($configList, false);
        $this->subject['key2'] = 'value2b';
        unset($this->subject['key3']);
        $this->subject['key4'] = 'value4b';

        $this->assertEquals('value1', $this->subject['key1']);
        $this->assertEquals('value2b', $this->subject['key2']);
        $this->assertNull($this->subject['key3']);
        $this->assertEquals('value4b', $this->subject['key4']);
    }

    /** @test */
    public function overrideWithAdditionalConfiguration(): void
    {
        $configList = [
            [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
        ];
        $this->subject = new Configuration($configList, false);
        $this->assertEquals('value1', $this->subject->get('key1'));
        $this->assertEquals('value2', $this->subject->get('key2'));
        $this->assertNull($this->subject->get('key3'));

        $this->subject->set('key2', 'value2b');
        $this->subject->set('key3', 'value3b');
        $this->assertEquals('value1', $this->subject->get('key1'));
        $this->assertEquals('value2b', $this->subject->get('key2'));
        $this->assertEquals('value3b', $this->subject->get('key3'));

        $this->subject->addConfiguration([
            'key2' => 'value2c',
        ]);
        $this->assertEquals('value1', $this->subject->get('key1'));
        $this->assertEquals('value2c', $this->subject->get('key2'));
        $this->assertEquals('value3b', $this->subject->get('key3'));

        $this->subject->set('key2', 'value2d');
        $this->assertEquals('value1', $this->subject->get('key1'));
        $this->assertEquals('value2d', $this->subject->get('key2'));
        $this->assertEquals('value3b', $this->subject->get('key3'));

        $this->assertEquals([
            [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
            [
                'key2' => 'value2b',
                'key3' => 'value3b',
            ],
            [
                'key2' => 'value2c',
            ],
            [
                'key2' => 'value2d',
            ],
        ], $this->subject->toArray());
    }

    /**
     * @return array{0:array{0:true},1:array{0:false}}
     */
    public function readonlyStateProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider readonlyStateProvider
     */
    public function readonlyConfigAddConfigurationIsBadMethodCall(bool $readonly): void
    {
        $configList = [[]];

        if ($readonly) {
            $this->expectException(BadMethodCallException::class);
        }

        $this->subject = new Configuration($configList, readonly: $readonly);
        $this->subject->addConfiguration([
            'key1' => 'value1',
        ]);

        if (!$readonly) {
            $this->assertEquals('value1', $this->subject->get('key1'));
        }
    }

    /**
     * @test
     *
     * @dataProvider readonlyStateProvider
     */
    public function readonlyConfigSetIsBadMethodCall(bool $readonly): void
    {
        $configList = [[]];

        if ($readonly) {
            $this->expectException(BadMethodCallException::class);
        }

        $this->subject = new Configuration($configList, readonly: $readonly);
        $this->subject->set('key1', 'value1');

        if (!$readonly) {
            $this->assertEquals('value1', $this->subject->get('key1'));
        }
    }

    /**
     * @test
     *
     * @dataProvider readonlyStateProvider
     */
    public function readonlyConfigOffsetSetIsBadMethodCall(bool $readonly): void
    {
        $configList = [[]];

        if ($readonly) {
            $this->expectException(BadMethodCallException::class);
        }

        $this->subject = new Configuration($configList, readonly: $readonly);
        $this->subject['key1'] = 'value1';

        if (!$readonly) {
            $this->assertEquals('value1', $this->subject->get('key1'));
        }
    }

    /**
     * @test
     *
     * @dataProvider readonlyStateProvider
     */
    public function readonlyConfigUnsetIsBadMethodCall(bool $readonly): void
    {
        $configList = [['key1' => 'value1']];

        if ($readonly) {
            $this->expectException(BadMethodCallException::class);
        }

        $this->subject = new Configuration($configList, readonly: $readonly);
        $this->subject->unset('key1');

        if (!$readonly) {
            $this->assertNull($this->subject->get('key1'));
        }
    }

    /**
     * @test
     *
     * @dataProvider readonlyStateProvider
     */
    public function readonlyConfigOffsetUnsetIsBadMethodCall(bool $readonly): void
    {
        $configList = [['key1' => 'value1']];

        if ($readonly) {
            $this->expectException(BadMethodCallException::class);
        }

        $this->subject = new Configuration($configList, readonly: $readonly);
        unset($this->subject['key1']);

        if (!$readonly) {
            $this->assertNull($this->subject->get('key1'));
        }
    }

    /**
     * @test
     *
     * @dataProvider readonlyStateProvider
     */
    public function readonlyConfigReportsAccurately(bool $readonly): void
    {
        $configList = [[]];
        $this->subject = new Configuration($configList, readonly: $readonly);
        $this->assertEquals($readonly, $this->subject->isReadonly());
    }
}
