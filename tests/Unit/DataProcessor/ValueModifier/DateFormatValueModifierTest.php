<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\DateFormatValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifier;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValue;
use PHPUnit\Framework\Attributes\Test;

class DateFormatValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'dateFormat';

    protected const CLASS_NAME = DateFormatValueModifier::class;

    protected const DEFAULT_CONFIG = [
        DateFormatValueModifier::KEY_FORMAT => DateFormatValueModifier::DEFAULT_FORMAT,
    ];

    /**
     * @return array<string,array{0:mixed,1:mixed,2:?array<string,mixed>}>
     */
    public static function modifyProvider(): array
    {
        return [
            'nullReturnsNull' => [null, null, null],
            'dateTimeValueWithNewFormat' => [
                new DateTimeValue('2025-01-21', 'Y-m-d'),
                '21.01.2025',
                [DateFormatValueModifier::KEY_FORMAT => 'd.m.Y'],
            ],
            'stringDateWithFormat' => [
                '2025-01-21',
                '21.01.2025',
                [DateFormatValueModifier::KEY_FORMAT => 'd.m.Y'],
            ],
            'timestampStringWithFormat' => [
                '1737417600',
                '2025-01-21',
                [DateFormatValueModifier::KEY_FORMAT => 'Y-m-d'],
            ],
            'defaultFormatIsUsed' => [
                new DateTimeValue('2025-01-21', 'd.m.Y'),
                '2025-01-21',
                null,
            ],
        ];
    }

    #[Test]
    public function invalidStringReturnsOriginalValueAndLogsWarning(): void
    {
        $this->logger->expects($this->once())->method('warning');

        $config = [
            ValueModifier::KEY_ENABLED => true,
            DateFormatValueModifier::KEY_FORMAT => 'Y-m-d',
        ];
        $output = $this->processValueModifier($config, 'not-a-date');

        $this->assertEquals('not-a-date', $output);
    }
}
