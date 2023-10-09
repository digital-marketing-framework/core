<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

class BooleanValue extends Value implements BooleanValueInterface
{
    protected bool $value;

    final public function __construct(
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

    public function negated(): BooleanValueInterface
    {
        return new BooleanValue(!$this->value, $this->true, $this->false);
    }

    public function getTrueValue(): string
    {
        return $this->true;
    }

    public function setTrueValue(string $true): void
    {
        $this->true = $true;
    }

    public function getFalseValue(): string
    {
        return $this->false;
    }

    public function setFalseValue(string $false): void
    {
        $this->false = $false;
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
