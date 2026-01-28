<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\DateValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValueInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(DateValueSource::class)]
class DateValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'date';

    /**
     * @return array<string,array{0:mixed,1:?string,2:string}>
     */
    public static function valuesDataProvider(): array
    {
        return [
            'nullReturnsNull' => [null, null, 'Y-m-d'],
            'dateStringWithDefaultFormat' => ['2025-01-21', '2025-01-21', 'Y-m-d'],
            'dateStringWithCustomFormat' => ['2025-01-21', '21.01.2025', 'd.m.Y'],
            'europeanDateStringWithDefaultFormat' => ['21.01.2025', '2025-01-21', 'Y-m-d'],
            'timestampStringWithDefaultFormat' => ['1737417600', '2025-01-21', 'Y-m-d'],
            'invalidDateReturnsNull' => ['not-a-date', null, 'Y-m-d'],
        ];
    }

    #[Test]
    #[DataProvider('valuesDataProvider')]
    public function dateValue(mixed $value, ?string $expectedResult, string $format): void
    {
        $this->data['field1'] = $value;
        $config = [
            DateValueSource::KEY_VALUE => $this->getValueConfiguration([
                FieldValueSource::KEY_FIELD_NAME => 'field1',
            ], 'field'),
            DateValueSource::KEY_FORMAT => $format,
        ];
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        if ($expectedResult === null) {
            $this->assertNull($output);
        } else {
            $this->assertInstanceOf(DateTimeValueInterface::class, $output);
            $this->assertEquals($expectedResult, (string)$output);
        }
    }
}
