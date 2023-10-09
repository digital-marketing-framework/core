<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\BooleanValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

class BooleanValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'boolean';

    protected const CLASS_NAME = BooleanValueSource::class;

    protected const DEFAULT_CONFIG = [
        BooleanValueSource::KEY_VALUE => BooleanValueSource::DEFAULT_VALUE,
        BooleanValueSource::KEY_TRUE => BooleanValueSource::DEFAULT_TRUE,
        BooleanValueSource::KEY_FALSE => BooleanValueSource::DEFAULT_FALSE,
    ];

    /** @test */
    public function emptyConfigurationReturnsNull(): void
    {
        $output = $this->processValueSource([]);
        $this->assertNull($output);
    }

    /**
     * @return array<array<mixed>>
     */
    public function valuesDataProvider(): array
    {
        return [
            [null, null],
            ['', false],
            ['0', false],
            ['1', true],
            ['abc', true],
            [new MultiValue(), false],
            [new MultiValue(['1']), true],

            ['0', false, 'a', 'b'],
            ['1', true, 'a', 'b'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider valuesDataProvider
     */
    public function booleanValue(mixed $value, ?bool $expectedResult, mixed $true = null, mixed $false = null): void
    {
        $subConfig = ['subConfigKey' => 'subConfigValue'];
        $this->dataProcessor->method('processValue')->with($subConfig)->willReturn($value);
        $config = [
            BooleanValueSource::KEY_VALUE => $subConfig,
        ];
        if ($true !== null) {
            $config[BooleanValueSource::KEY_TRUE] = $true;
        }

        if ($false !== null) {
            $config[BooleanValueSource::KEY_FALSE] = $false;
        }

        $output = $this->processValueSource($config);
        if ($expectedResult === null) {
            $this->assertNull($output);
        } else {
            $this->assertInstanceOf(BooleanValueInterface::class, $output);
            $this->assertEquals($expectedResult, $output->getValue());
            if ($expectedResult) {
                $this->assertEquals($true ?? '1', (string)$output);
            } else {
                $this->assertEquals($false ?? '0', (string)$output);
            }
        }
    }
}
