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
     * @return array<array{0:array<mixed>,1:array<array<string,mixed>>,2:array<mixed>}>
     */
    public function multiValueDataProvider(): array
    {
        return [
            [
                [],
                [
                    ['confKey1' => 'confValue1'],
                    ['confKey2' => 'confValue2'],
                    ['confKey3' => 'confValue3'],
                ],
                [
                    null,
                    null,
                    null,
                ],
            ],
            [
                ['foo'],
                [
                    ['confKey1' => 'confValue1'],
                    ['confKey2' => 'confValue2'],
                    ['confKey3' => 'confValue3'],
                ],
                [
                    null,
                    'foo',
                    null,
                ],
            ],
            [
                ['', 'a'],
                [
                    ['confKey1' => 'confValue1'],
                    ['confKey2' => 'confValue2'],
                    ['confKey3' => 'confValue3'],
                ],
                [
                    null,
                    '',
                    'a',
                ],
            ],
            [
                ['a', 'b', 'c'],
                [
                    ['confKey1' => 'confValue1'],
                    ['confKey2' => 'confValue2'],
                    ['confKey3' => 'confValue3'],
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
        $listConfig = [];
        foreach ($subConfigurations as $index => $subConfig) {
            $listConfig[$index] = $this->createListItem($subConfig, $index, $index * 10);
        }

        $config = [
            MultiValueValueSource::KEY_VALUES => $listConfig,
        ];
        $this->dataProcessor->method('processValue')->withConsecutive(...$with)->willReturnOnConsecutiveCalls(...$subResults);
        $output = $this->processValueSource($config);
        $this->assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        $this->assertMultiValueEquals($expectedResult, $output);
    }
}
