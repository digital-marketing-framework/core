<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\IntegerValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\IntegerValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

class IntegerValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'integer';

    protected const CLASS_NAME = IntegerValueSource::class;

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
            ['', null],
            ['0', 0],
            ['1', 1],
            ['42', 42],
            ['-13', -13],
            ['abc', null],
            [new MultiValue(), null],
            [new MultiValue(['1']), 1],
            [new MultiValue(['1', '2']), null],
        ];
    }

    /**
     * @test
     *
     * @dataProvider valuesDataProvider
     */
    public function integerValue(mixed $value, ?int $expectedResult): void
    {
        $subConfig = ['subConfigKey' => 'subConfigValue'];
        $this->dataProcessor->method('processValue')->with($subConfig)->willReturn($value);
        $config = [
            IntegerValueSource::KEY_VALUE => $subConfig,
        ];
        $output = $this->processValueSource($config);
        if ($expectedResult === null) {
            $this->assertNull($output);
        } else {
            $this->assertInstanceOf(IntegerValueInterface::class, $output);
            $this->assertEquals($expectedResult, $output->getValue());
        }
    }
}
