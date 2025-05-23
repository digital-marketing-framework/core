<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\MultiValueValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(MultiValueValueSource::class)]
class MultiValueValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'multiValue';

    protected const MULTI_VALUE_CLASS_NAME = MultiValue::class;

    #[Test]
    public function emptyConfigurationReturnsEmptyMultiValue(): void
    {
        $output = $this->processValueSource($this->getValueSourceConfiguration([]));
        $this->assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        $this->assertMultiValueEmpty($output);
    }

    /**
     * @return array<array{0:array<string|ValueInterface|null>,1:array<string,mixed>}>
     */
    public static function multiValueDataProvider(): array
    {
        return [
            [
                [],
                [
                    MultiValueValueSource::KEY_VALUES => [
                        'id1' => static::createMapItem('key1', static::getValueConfiguration([], 'null'), 'id1', 10),
                        'id2' => static::createMapItem('key2', static::getValueConfiguration([], 'null'), 'id2', 20),
                        'id3' => static::createMapItem('key2', static::getValueConfiguration([], 'null'), 'id3', 30),
                    ],
                ],
            ],
            [
                [
                    'key2' => 'foo',
                ],
                [
                    MultiValueValueSource::KEY_VALUES => [
                        'id1' => static::createMapItem('key1', static::getValueConfiguration([], 'null'), 'id1', 10),
                        'id2' => static::createMapItem('key2', static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'foo'], 'constant'), 'id2', 20),
                        'id3' => static::createMapItem('key3', static::getValueConfiguration([], 'null'), 'id3', 30),
                    ],
                ],
            ],
            [
                [
                    'key2' => '',
                    'key3' => 'a',
                ],
                [
                    MultiValueValueSource::KEY_VALUES => [
                        'id1' => static::createMapItem('key1', static::getValueConfiguration([], 'null'), 'id1', 10),
                        'id2' => static::createMapItem('key2', static::getValueConfiguration([ConstantValueSource::KEY_VALUE => ''], 'constant'), 'id2', 20),
                        'id3' => static::createMapItem('key3', static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'), 'id3', 30),
                    ],
                ],
            ],
            [
                [
                    'key1' => 'a',
                    'key2' => 'b',
                    'key3' => 'c',
                ],
                [
                    MultiValueValueSource::KEY_VALUES => [
                        'id1' => static::createMapItem('key1', static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'), 'id1', 10),
                        'id2' => static::createMapItem('key2', static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'b'], 'constant'), 'id2', 20),
                        'id3' => static::createMapItem('key3', static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'c'], 'constant'), 'id3', 30),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string|ValueInterface|null> $expectedResult
     * @param array<string,mixed> $config
     */
    #[Test]
    #[DataProvider('multiValueDataProvider')]
    public function multiValue(array $expectedResult, array $config): void
    {
        $output = $this->processValueSource(static::getValueSourceConfiguration($config));
        static::assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        static::assertMultiValueEquals($expectedResult, $output);
    }
}
