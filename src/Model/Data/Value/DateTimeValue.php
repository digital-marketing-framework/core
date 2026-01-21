<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

use DateTime;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use Exception;
use Stringable;

class DateTimeValue extends Value implements Stringable, DateTimeValueInterface
{
    public const DEFAULT_FORMAT = 'Y-m-d';

    protected DateTime $date;

    final public function __construct(
        string|DateTime $date,
        protected string $format = self::DEFAULT_FORMAT,
    ) {
        $this->setDate($date);
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(string|DateTime $date): void
    {
        if (is_string($date) && is_numeric($date)) {
            $timestamp = $date;
            $date = new DateTime();
            $date->setTimestamp((int)$timestamp);
        } elseif (is_string($date)) {
            try {
                $date = new DateTime($date);
            } catch (Exception $e) {
                throw new DigitalMarketingFrameworkException($e->getMessage(), $e->getCode(), $e);
            }
        }

        $this->date = $date;
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
        ];
    }

    public static function unpack(array $packed): DateTimeValue
    {
        return new static(
            $packed['timestamp'],
            $packed['format']
        );
    }
}
