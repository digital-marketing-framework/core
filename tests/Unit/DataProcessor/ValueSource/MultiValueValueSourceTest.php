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
        $output = $this->processValueSource([]);
        $this->assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        $this->assertMultiValueEmpty($output);
    }

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
     * @test
     * @dataProvider multiValueDataProvider
     */
    public function multiValue(array $expectedResult, array $config, array $subResults): void
    {
        $with = array_map(function(array $subConfigItem) { return [$subConfigItem]; }, $config);
        $this->dataProcessor->method('processValue')->withConsecutive(...$with)->willReturnOnConsecutiveCalls(...$subResults);
        $output = $this->processValueSource($config);
        $this->assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        $this->assertMultiValueEquals($expectedResult, $output);
    }
}
