<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

use DateMalformedStringException;
use DateTime;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

class DateTimeValue extends Value
{
    public const DEFAULT_FORMAT = 'Y-m-d';

    protected DateTime $date;

    public function __construct(
        string|int|DateTime $date,
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
        if (is_numeric($date)) {
            $timestamp = $date;
            $date = new DateTime();
            $date->setTimestamp((int)$timestamp);
        } elseif (is_string($date)) {
            try {
                $date = new DateTime($date);
            } catch (DateMalformedStringException $e) {
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
            'timestamp' => $this->date->getTimestamp(),
            'format' => $this->format,
        ];
    }

    public static function unpack(array $packed): ValueInterface
    {
        return new static(
            $packed['timestamp'],
            $packed['format']
        );
    }
}
