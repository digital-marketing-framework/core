<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\MultiValueValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

class MultiValueValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'multiValue';

    protected const CLASS_NAME = MultiValueValueSource::class;

    protected const MULTI_VALUE_CLASS_NAME = MultiValue::class;

    /** @test */
    public function emptyConfigurationReturnsEmptyMultiValue(): void
    {
        $output = $this->processValueSource([
            MultiValueValueSource::KEY_VALUES => [],
        ]);
        $this->assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        $this->assertMultiValueEmpty($output);
    }

    /**
     * @return array<array{0:array<mixed>,1:array<string,array<string,mixed>>,2:array<mixed>}>
     */
    public function multiValueDataProvider(): array
    {
        return [
            [
                [],
                [
                    'key1' => ['confKey1' => 'confValue1'],
                    'key2' => ['confKey2' => 'confValue2'],
                    'key3' => ['confKey3' => 'confValue3'],
                ],
                [
                    null,
                    null,
                    null,
                ],
            ],
            [
                [
                    'key2' => 'foo',
                ],
                [
                    'key1' => ['confKey1' => 'confValue1'],
                    'key2' => ['confKey2' => 'confValue2'],
                    'key3' => ['confKey3' => 'confValue3'],
                ],
                [
                    null,
                    'foo',
                    null,
                ],
            ],
            [
                [
                    'key2' => '',
                    'key3' => 'a',
                ],
                [
                    'key1' => ['confKey1' => 'confValue1'],
                    'key2' => ['confKey2' => 'confValue2'],
                    'key3' => ['confKey3' => 'confValue3'],
                ],
                [
                    null,
                    '',
                    'a',
                ],
            ],
            [
                [
                    'key1' => 'a',
                    'key2' => 'b',
                    'key3' => 'c',
                ],
                [
                    'key1' => ['confKey1' => 'confValue1'],
                    'key2' => ['confKey2' => 'confValue2'],
                    'key3' => ['confKey3' => 'confValue3'],
                ],
                [
                    'a',
                    'b',
                    'c',
                ],
            ],
        ];
    }

    /**
     * @param array<mixed> $expectedResult
     * @param array<array<string,mixed>> $subConfigurations
     * @param array<mixed> $subResults
     *
     * @test
     *
     * @dataProvider multiValueDataProvider
     */
    public function multiValue(array $expectedResult, array $subConfigurations, array $subResults): void
    {
        $with = array_map(static function (array $subConfigItem) {
            return [$subConfigItem];
        }, $subConfigurations);
        $mapConfig = [];
        $index = 0;
        foreach ($subConfigurations as $key => $subConfig) {
            $id = 'id' . $index;
            $mapConfig[$id] = $this->createMapItem($key, $subConfig, $id, $index * 10);
            ++$index;
        }

        $config = [
            MultiValueValueSource::KEY_VALUES => $mapConfig,
        ];
        $this->dataProcessor->method('processValue')->withConsecutive(...$with)->willReturnOnConsecutiveCalls(...$subResults);
        $output = $this->processValueSource($config);
        $this->assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        $this->assertMultiValueEquals($expectedResult, $output);
    }
}
