<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FirstOfValueSource;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends ValueSourceTestBase<FirstOfValueSource>
 */
class FirstOfValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'firstOf';

    protected const CLASS_NAME = FirstOfValueSource::class;

    #[Test]
    public function emptyConfigurationLeadsToNullValue(): void
    {
        $output = $this->processValueSource([]);
        $this->assertNull($output);
    }

    /**
     * @return array<array{0:mixed,1:array<array<string,mixed>>,2:array<mixed>}>
     */
    public static function firstOfDataProvider(): array
    {
        return [
            [
                null,
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
                'foo',
                [
                    ['confKey1' => 'confValue1'],
                    ['confKey2' => 'confValue2'],
                    ['confKey3' => 'confValue3'],
                ],
                [
                    null,
                    'foo',
                ],
            ],
            [
                '',
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
                'a',
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
     * @param array<array<string,mixed>> $subConfigurations
     * @param array<mixed> $subResults
     */
    #[Test]
    #[DataProvider('firstOfDataProvider')]
    public function firstOf(mixed $expectedResult, array $subConfigurations, array $subResults): void
    {
        $with = array_map(static fn (array $subConfigItem) => [$subConfigItem], $subConfigurations);
        $with = array_splice($with, 0, count($subResults));

        $listConfig = [];
        foreach ($subConfigurations as $index => $subConfig) {
            $listConfig[$index] = $this->createListItem($subConfig, $index, $index * 10);
        }

        $config = [
            FirstOfValueSource::KEY_VALUE_LIST => $listConfig,
        ];
        $this->withConsecutiveWillReturn($this->dataProcessor, 'processValue', $with, $subResults);
        $output = $this->processValueSource($config);
        $this->assertEquals($expectedResult, $output);
    }
}
