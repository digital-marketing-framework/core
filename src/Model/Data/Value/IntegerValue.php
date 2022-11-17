<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

class IntegerValue implements IntegerValueInterface
{
    protected int $value;

    public function __construct(mixed $value)
    {
        $this->value = (int)$value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = (int)$value;
    }

    public function pack(): array
    {
        return [
            'value' => $this->value,
        ];
    }

    public static function unpack(array $packed): IntegerValue
    {
        return new static($packed['value']);
    }
}
