<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FirstOfValueSource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(FirstOfValueSource::class)]
class FirstOfValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'firstOf';

    /**
     * @return array<array{0:mixed,1:array<string,mixed>}>
     */
    public static function firstOfDataProvider(): array
    {
        return [
            [
                null,
                [
                    FirstOfValueSource::KEY_VALUE_LIST => [
                        'id1' => static::createListitem(static::getValueConfiguration([], 'null'), 'id1', 10),
                        'id2' => static::createListitem(static::getValueConfiguration([], 'null'), 'id2', 20),
                    ],
                ],
            ],
            [
                'foo',
                [
                    FirstOfValueSource::KEY_VALUE_LIST => [
                        'id1' => static::createListitem(static::getValueConfiguration([], 'null'), 'id1', 10),
                        'id2' => static::createListitem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'foo'], 'constant'), 'id2', 20),
                        'id3' => static::createListitem(static::getValueConfiguration([], 'null'), 'id3', 30),
                    ],
                ],
            ],
            [
                '',
                [
                    FirstOfValueSource::KEY_VALUE_LIST => [
                        'id1' => static::createListitem(static::getValueConfiguration([], 'null'), 'id1', 10),
                        'id2' => static::createListitem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => ''], 'constant'), 'id2', 20),
                        'id3' => static::createListitem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'), 'id3', 30),
                    ],
                ],
            ],
            [
                'a',
                [
                    FirstOfValueSource::KEY_VALUE_LIST => [
                        'id1' => static::createListitem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'), 'id1', 10),
                        'id2' => static::createListitem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'b'], 'constant'), 'id2', 20),
                        'id3' => static::createListitem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'c'], 'constant'), 'id3', 30),
                    ],
                ],
            ],
            [
                'b',
                [
                    FirstOfValueSource::KEY_VALUE_LIST => [
                        'id1' => static::createListitem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'), 'id1', 20),
                        'id2' => static::createListitem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'b'], 'constant'), 'id2', 10),
                        'id3' => static::createListitem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'c'], 'constant'), 'id3', 30),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string,mixed> $expectedResult
     * @param array<string,mixed> $config
     */
    #[Test]
    #[DataProvider('firstOfDataProvider')]
    public function firstOf(mixed $expectedResult, array $config): void
    {
        $output = $this->processValueSource(static::getValueSourceConfiguration($config));
        static::assertEquals($expectedResult, $output);
    }
}
