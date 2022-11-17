<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

class BooleanValue implements ValueInterface
{
    protected bool $value;

    public function __construct(
        mixed $value, 
        protected string $true = '1',
        protected string $false = '0',
    ) {
        $this->value = (bool)$value;
    }

    public function __toString(): string
    {
        return $this->value ? $this->true : $this->false;
    }

    public function negated(): string
    {
        return $this->value ? $this->false : $this->true;
    }

    public function getThenValue(): string
    {
        return $this->then;
    }

    public function setThenValue(string $then): void
    {
        $this->then = $then;
    }

    public function getElseValue(): string
    {
        return $this->else;
    }

    public function setElseValue(string $else): void
    {
        $this->else = $else;
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = (bool)$value;
    }

    public function pack(): array
    {
        return [
            'value' => $this->value,
            'true' => $this->true,
            'false' => $this->false,
        ];
    }

    public static function unpack(array $packed): BooleanValue
    {
        return new static($packed['value'], $packed['true'], $packed['false']);
    }
}
