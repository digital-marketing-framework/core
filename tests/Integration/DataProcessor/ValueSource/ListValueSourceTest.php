<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ListValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ListValueSource::class)]
class ListValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'list';

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
    public static function listDataProvider(): array
    {
        return [
            [
                [],
                [
                    ListValueSource::KEY_VALUES => [
                        'id1' => static::createListItem(static::getValueConfiguration([], 'null'), 'id1', 10),
                        'id2' => static::createListItem(static::getValueConfiguration([], 'null'), 'id2', 20),
                        'id3' => static::createListItem(static::getValueConfiguration([], 'null'), 'id3', 30),
                    ],
                ],
            ],
            [
                ['foo'],
                [
                    ListValueSource::KEY_VALUES => [
                        'id1' => static::createListItem(static::getValueConfiguration([], 'null'), 'id1', 10),
                        'id2' => static::createListItem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'foo'], 'constant'), 'id2', 20),
                        'id3' => static::createListItem(static::getValueConfiguration([], 'null'), 'id3', 30),
                    ],
                ],
            ],
            [
                ['', 'a'],
                [
                    ListValueSource::KEY_VALUES => [
                        'id1' => static::createListItem(static::getValueConfiguration([], 'null'), 'id1', 10),
                        'id2' => static::createListItem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => ''], 'constant'), 'id2', 20),
                        'id3' => static::createListItem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'), 'id3', 30),
                    ],
                ],
            ],
            [
                ['a', 'b', 'c'],
                [
                    ListValueSource::KEY_VALUES => [
                        'id1' => static::createListItem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'), 'id1', 10),
                        'id2' => static::createListItem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'b'], 'constant'), 'id2', 20),
                        'id3' => static::createListItem(static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'c'], 'constant'), 'id3', 30),
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
    #[DataProvider('listDataProvider')]
    public function list(array $expectedResult, array $config): void
    {
        $output = $this->processValueSource(static::getValueSourceConfiguration($config));
        static::assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        static::assertMultiValueEquals($expectedResult, $output);
    }
}
