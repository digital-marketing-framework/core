<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ListValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends ValueSourceTestBase<ListValueSource>
 */
class ListValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'list';

    protected const CLASS_NAME = ListValueSource::class;

    protected const MULTI_VALUE_CLASS_NAME = MultiValue::class;

    #[Test]
    public function emptyConfigurationReturnsEmptyMultiValue(): void
    {
        $output = $this->processValueSource([
            ListValueSource::KEY_VALUES => [],
        ]);
        $this->assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        $this->assertMultiValueEmpty($output);
    }

    /**
     * @return array<array{0:array<mixed>,1:array<array<string,mixed>>,2:array<mixed>}>
     */
    public static function listDataProvider(): array
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
     */
    #[Test]
    #[DataProvider('listDataProvider')]
    public function list(array $expectedResult, array $subConfigurations, array $subResults): void
    {
        $with = array_map(static fn (array $subConfigItem) => [$subConfigItem], $subConfigurations);
        $listConfig = [];
        foreach ($subConfigurations as $index => $subConfig) {
            $listConfig[$index] = $this->createListItem($subConfig, $index, $index * 10);
        }

        $config = [
            ListValueSource::KEY_VALUES => $listConfig,
        ];
        $this->withConsecutiveWillReturn($this->dataProcessor, 'processValue', $with, $subResults);
        $output = $this->processValueSource($config);
        $this->assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        $this->assertMultiValueEquals($expectedResult, $output);
    }
}
