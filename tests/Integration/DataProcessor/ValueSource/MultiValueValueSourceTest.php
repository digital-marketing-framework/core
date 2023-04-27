<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\MultiValueValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers MultiValueValueSource
 */
class MultiValueValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'multiValue';

    protected const MULTI_VALUE_CLASS_NAME = MultiValue::class;

    /** @test */
    public function emptyConfigurationReturnsEmptyMultiValue(): void
    {
        $output = $this->processValueSource($this->getValueSourceConfiguration([]));
        $this->assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        $this->assertMultiValueEmpty($output);
    }

    public function multiValueDataProvider(): array
    {
        return [
            [
                [],
                [
                    $this->getValueConfiguration([], 'null'),
                    $this->getValueConfiguration([], 'null'),
                    $this->getValueConfiguration([], 'null'),
                ],
            ],
            [
                ['foo'],
                [
                    $this->getValueConfiguration([], 'null'),
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'foo'], 'constant'),
                    $this->getValueConfiguration([], 'null'),
                ],
            ],
            [
                ['', 'a'],
                [
                    $this->getValueConfiguration([], 'null'),
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => ''], 'constant'),
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'),
                ],
            ],
            [
                ['a', 'b', 'c'],
                [
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'),
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'b'], 'constant'),
                    $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'c'], 'constant'),
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider multiValueDataProvider
     */
    public function multiValue(array $expectedResult, array $config): void
    {
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        $this->assertMultiValue($output, static::MULTI_VALUE_CLASS_NAME);
        $this->assertMultiValueEquals($expectedResult, $output);
    }
}
