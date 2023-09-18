<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\IntegerValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\IntegerValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueSource\IntegerValueSource
 */
class IntegerValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'integer';

    /** @test */
    public function emptyConfigurationReturnsNull(): void
    {
        $output = $this->processValueSource($this->getValueSourceConfiguration([]));
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
        $this->data['field1'] = $value;
        $config = [
            IntegerValueSource::KEY_VALUE => $this->getValueConfiguration([
                FieldValueSource::KEY_FIELD_NAME => 'field1',
            ], 'field'),
        ];
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        if ($expectedResult === null) {
            $this->assertNull($output);
        } else {
            $this->assertInstanceOf(IntegerValueInterface::class, $output);
            $this->assertEquals($expectedResult, $output->getValue());
        }
    }
}
