<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

use DateTime;
use DateTimeZone;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use Exception;
use Stringable;

class DateTimeValue extends Value implements Stringable, DateTimeValueInterface
{
    public const DEFAULT_FORMAT = 'Y-m-d';

    public const DEFAULT_TIMEZONE = 'UTC';

    protected DateTime $date;

    final public function __construct(
        string $date,
        protected string $format = self::DEFAULT_FORMAT,
        string $timezone = self::DEFAULT_TIMEZONE,
    ) {
        $this->initDate($date, $timezone);
    }

    protected function initDate(string $date, string $timezone): void
    {
        try {
            $tz = new DateTimeZone($timezone);

            if (is_numeric($date)) {
                $this->date = new DateTime('now', $tz);
                $this->date->setTimestamp((int)$date);
            } else {
                $this->date = new DateTime($date, $tz);
            }
        } catch (Exception $e) {
            throw new DigitalMarketingFrameworkException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function getTimezone(): string
    {
        return $this->date->getTimezone()->getName();
    }

    public function setTimezone(string $timezone): void
    {
        $this->date->setTimezone(new DateTimeZone($timezone));
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function formatted(string $format): string
    {
        return $this->date->format($format);
    }

    public function __toString(): string
    {
        return $this->formatted($this->format);
    }

    public function pack(): array
    {
        return [
            'timestamp' => (string)$this->date->getTimestamp(),
            'format' => $this->format,
            'timezone' => $this->getTimezone(),
        ];
    }

    public static function unpack(array $packed): DateTimeValue
    {
        return new static(
            $packed['timestamp'],
            $packed['format'],
            $packed['timezone'] ?? self::DEFAULT_TIMEZONE,
        );
    }
}
