<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

use DateTime;

interface DateTimeValueInterface extends ValueInterface
{
    public function getDate(): DateTime;

    public function setDate(DateTime $date): void;

    public function getTimezone(): string;

    public function setTimezone(string $timezone): void;

    public function getFormat(): string;

    public function setFormat(string $format): void;

    public function formatted(string $format): string;
}
