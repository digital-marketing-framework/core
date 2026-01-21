<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\DateModifyValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifier;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValue;
use PHPUnit\Framework\Attributes\Test;

class DateModifyValueModifierTest extends ValueModifierTestBase
{
    protected const KEYWORD = 'dateModify';

    protected const CLASS_NAME = DateModifyValueModifier::class;

    protected const DEFAULT_CONFIG = [
        DateModifyValueModifier::KEY_MODIFIER => DateModifyValueModifier::DEFAULT_MODIFIER,
    ];

    /**
     * @return array<string,array{0:mixed,1:mixed,2:?array<string,mixed>}>
     */
    public static function modifyProvider(): array
    {
        return [
            'nullReturnsNull' => [null, null, null],
            'addOneDay' => [
                new DateTimeValue('2025-01-21', 'Y-m-d'),
                '2025-01-22',
                [DateModifyValueModifier::KEY_MODIFIER => '+1 day'],
            ],
            'subtractOneMonth' => [
                new DateTimeValue('2025-01-21', 'Y-m-d'),
                '2024-12-21',
                [DateModifyValueModifier::KEY_MODIFIER => '-1 month'],
            ],
            'addOneYear' => [
                new DateTimeValue('2025-01-21', 'Y-m-d'),
                '2026-01-21',
                [DateModifyValueModifier::KEY_MODIFIER => '+1 year'],
            ],
            'addMultipleDays' => [
                new DateTimeValue('2025-01-21', 'Y-m-d'),
                '2025-01-31',
                [DateModifyValueModifier::KEY_MODIFIER => '+10 days'],
            ],
            'stringDateModification' => [
                '2025-01-21',
                '2025-01-22',
                [DateModifyValueModifier::KEY_MODIFIER => '+1 day'],
            ],
            'preservesFormat' => [
                new DateTimeValue('2025-01-21', 'd.m.Y'),
                '22.01.2025',
                [DateModifyValueModifier::KEY_MODIFIER => '+1 day'],
            ],
            'defaultModifierIsUsed' => [
                new DateTimeValue('2025-01-21', 'Y-m-d'),
                '2025-01-22',
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
            DateModifyValueModifier::KEY_MODIFIER => '+1 day',
        ];
        $output = $this->processValueModifier($config, 'not-a-date');

        $this->assertEquals('not-a-date', $output);
    }
}
