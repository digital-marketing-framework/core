<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Data;

use DateTime;
use DigitalMarketingFramework\Core\Model\Data\Value\DateTimeValue;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends FieldTestBase<DateTimeValue>
 */
class DateTimeValueTest extends FieldTestBase
{
    protected const FIELD_CLASS = DateTimeValue::class;

    protected function createField(mixed ...$arguments): DateTimeValue
    {
        return new DateTimeValue(
            $arguments[0] ?? '2025-01-21',
            $arguments[1] ?? 'Y-m-d',
        );
    }

    #[Test]
    public function initWithString(): void
    {
        $this->subject = $this->createField('2025-01-21', 'Y-m-d');
        $this->assertEquals('2025-01-21', $this->subject->getDate()->format('Y-m-d'));
        $this->assertEquals('Y-m-d', $this->subject->getFormat());
    }

    #[Test]
    public function initWithTimestamp(): void
    {
        $timestamp = '1737417600'; // 2025-01-21 00:00:00 UTC
        $this->subject = new DateTimeValue($timestamp, 'Y-m-d');
        $this->assertEquals((int)$timestamp, $this->subject->getDate()->getTimestamp());
    }

    #[Test]
    public function initWithDateTime(): void
    {
        $dateTime = new DateTime('2025-01-21');
        $this->subject = new DateTimeValue($dateTime, 'd.m.Y');
        $this->assertEquals('2025-01-21', $this->subject->getDate()->format('Y-m-d'));
        $this->assertEquals('d.m.Y', $this->subject->getFormat());
    }

    #[Test]
    public function init(): void
    {
        $this->subject = $this->createField();
        $this->assertInstanceOf(DateTime::class, $this->subject->getDate());
        $this->assertEquals('Y-m-d', $this->subject->getFormat());
    }

    #[Test]
    public function formatted(): void
    {
        $this->subject = $this->createField('2025-01-21', 'Y-m-d');
        $this->assertEquals('21.01.2025', $this->subject->formatted('d.m.Y'));
        $this->assertEquals('01/21/2025', $this->subject->formatted('m/d/Y'));
    }

    #[Test]
    public function setFormat(): void
    {
        $this->subject = $this->createField('2025-01-21', 'Y-m-d');
        $this->assertEquals('2025-01-21', (string)$this->subject);

        $this->subject->setFormat('d.m.Y');
        $this->assertEquals('21.01.2025', (string)$this->subject);
    }

    #[Test]
    public function setDate(): void
    {
        $this->subject = $this->createField('2025-01-21', 'Y-m-d');
        $this->assertEquals('2025-01-21', (string)$this->subject);

        $this->subject->setDate('2025-12-25');
        $this->assertEquals('2025-12-25', (string)$this->subject);
    }

    /**
     * @return array<array{0:array<mixed>,1:string}>
     */
    public static function castToStringProvider(): array
    {
        return [
            [['2025-01-21', 'Y-m-d'], '2025-01-21'],
            [['2025-01-21', 'd.m.Y'], '21.01.2025'],
            [['2025-12-25', 'm/d/Y'], '12/25/2025'],
        ];
    }

    /**
     * @return array<array{0:array<mixed>,1:array{timestamp:string,format:string}}>
     */
    public static function packProvider(): array
    {
        $timestamp = (string)(new DateTime('2025-01-21'))->getTimestamp();

        return [
            [
                ['2025-01-21', 'Y-m-d'],
                [
                    'timestamp' => $timestamp,
                    'format' => 'Y-m-d',
                ],
            ],
            [
                ['2025-01-21', 'd.m.Y'],
                [
                    'timestamp' => $timestamp,
                    'format' => 'd.m.Y',
                ],
            ],
        ];
    }
}
