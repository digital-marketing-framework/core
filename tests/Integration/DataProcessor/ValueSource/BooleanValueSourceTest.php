<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\BooleanValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(BooleanValueSource::class)]
class BooleanValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'boolean';

    /**
     * @return array<array<mixed>>
     */
    public static function valuesDataProvider(): array
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

    #[Test]
    #[DataProvider('valuesDataProvider')]
    public function booleanValue(mixed $value, ?bool $expectedResult, mixed $true = null, mixed $false = null): void
    {
        $this->data['field1'] = $value;
        $config = [
            BooleanValueSource::KEY_VALUE => $this->getValueConfiguration([
                FieldValueSource::KEY_FIELD_NAME => 'field1',
            ], 'field'),
        ];
        if ($true !== null) {
            $config[BooleanValueSource::KEY_TRUE] = $true;
        }

        if ($false !== null) {
            $config[BooleanValueSource::KEY_FALSE] = $false;
        }

        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
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
