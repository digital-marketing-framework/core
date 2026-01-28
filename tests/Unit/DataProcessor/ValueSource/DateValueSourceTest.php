<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\DateValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValue;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValueInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends ValueSourceTestBase<DateValueSource>
 */
class DateValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'date';

    protected const CLASS_NAME = DateValueSource::class;

    protected const DEFAULT_CONFIG = [
        DateValueSource::KEY_FORMAT => DateValueSource::DEFAULT_FORMAT,
    ];

    #[Test]
    public function emptyConfigurationReturnsNull(): void
    {
        $output = $this->processValueSource([]);
        $this->assertNull($output);
    }

    #[Test]
    public function nullValueReturnsNull(): void
    {
        $subConfig = ['subConfigKey' => 'subConfigValue'];
        $this->dataProcessor->method('processValue')->with($subConfig)->willReturn(null);
        $config = [
            DateValueSource::KEY_VALUE => $subConfig,
        ];
        $output = $this->processValueSource($config);
        $this->assertNull($output);
    }

    /**
     * @return array<array<mixed>>
     */
    public static function validValuesDataProvider(): array
    {
        return [
            ['2025-01-21', 'Y-m-d', '2025-01-21'],
            ['2025-01-21', 'd.m.Y', '21.01.2025'],
            ['21.01.2025', 'Y-m-d', '2025-01-21'],
            ['1737417600', 'Y-m-d', '2025-01-21'], // timestamp as string
        ];
    }

    #[Test]
    #[DataProvider('validValuesDataProvider')]
    public function dateFromValidValue(mixed $inputValue, string $format, string $expectedOutput): void
    {
        $subConfig = ['subConfigKey' => 'subConfigValue'];
        $this->dataProcessor->method('processValue')->with($subConfig)->willReturn($inputValue);
        $config = [
            DateValueSource::KEY_VALUE => $subConfig,
            DateValueSource::KEY_FORMAT => $format,
        ];
        $output = $this->processValueSource($config);
        $this->assertInstanceOf(DateTimeValueInterface::class, $output);
        $this->assertEquals($expectedOutput, (string)$output);
    }

    #[Test]
    public function dateFromDateTimeValue(): void
    {
        $inputValue = new DateTimeValue('2025-01-21', 'Y-m-d');
        $subConfig = ['subConfigKey' => 'subConfigValue'];
        $this->dataProcessor->method('processValue')->with($subConfig)->willReturn($inputValue);
        $config = [
            DateValueSource::KEY_VALUE => $subConfig,
            DateValueSource::KEY_FORMAT => 'd.m.Y',
        ];
        $output = $this->processValueSource($config);
        $this->assertInstanceOf(DateTimeValueInterface::class, $output);
        $this->assertEquals('21.01.2025', (string)$output);
    }

    #[Test]
    public function invalidValueReturnsNullAndLogsWarning(): void
    {
        $this->logger->expects($this->once())->method('warning');

        $subConfig = ['subConfigKey' => 'subConfigValue'];
        $this->dataProcessor->method('processValue')->with($subConfig)->willReturn('not-a-date');
        $config = [
            DateValueSource::KEY_VALUE => $subConfig,
        ];
        $output = $this->processValueSource($config);
        $this->assertNull($output);
    }

    #[Test]
    public function emptyStringCreatesCurrentDateTime(): void
    {
        // Empty string is valid for DateTime - it creates current date/time
        $subConfig = ['subConfigKey' => 'subConfigValue'];
        $this->dataProcessor->method('processValue')->with($subConfig)->willReturn('');
        $config = [
            DateValueSource::KEY_VALUE => $subConfig,
        ];
        $output = $this->processValueSource($config);
        $this->assertInstanceOf(DateTimeValueInterface::class, $output);
    }

    #[Test]
    public function defaultFormatIsUsed(): void
    {
        $subConfig = ['subConfigKey' => 'subConfigValue'];
        $this->dataProcessor->method('processValue')->with($subConfig)->willReturn('2025-01-21');
        $config = [
            DateValueSource::KEY_VALUE => $subConfig,
            // No format specified - should use default
        ];
        $output = $this->processValueSource($config);
        $this->assertInstanceOf(DateTimeValueInterface::class, $output);
        $this->assertEquals('2025-01-21', (string)$output); // Default format is Y-m-d
    }
}
