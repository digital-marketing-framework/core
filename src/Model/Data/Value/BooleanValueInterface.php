<?php

namespace DigitalMarketingFramework\Core\Model\Data\Value;

interface BooleanValueInterface extends ValueInterface
{
    public function negated(): BooleanValueInterface;

    public function getTrueValue(): string;

    public function setTrueValue(string $then): void;

    public function getFalseValue(): string;

    public function setFalseValue(string $else): void;

    public function getValue(): bool;

    public function setValue(mixed $value): void;
}
