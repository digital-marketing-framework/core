<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class UnaryComparison extends Comparison
{
    public const KEY_VALUE = 'firstOperand';

    public const DEFAULT_VALUE = null;

    protected function compareValue(string|null|ValueInterface $value): bool
    {
        return true;
    }

    protected function compareAny(MultiValueInterface $value): bool
    {
        if (GeneralUtility::isEmpty($value)) {
            return $this->compareAnyEmpty();
        }

        foreach ($value as $subValue) {
            if ($this->compareValue($subValue)) {
                return true;
            }
        }

        return false;
    }

    protected function compareAll(MultiValueInterface $value): bool
    {
        if (GeneralUtility::isEmpty($value)) {
            return $this->compareAllEmpty();
        }

        foreach ($value as $subValue) {
            if (!$this->compareValue($subValue)) {
                return false;
            }
        }

        return true;
    }

    public function compare(): bool
    {
        $value = $this->dataProcessor->processValue($this->getConfig(static::KEY_VALUE), $this->context->copy());
        if (!static::handleMultiValuesIndividually() || !$value instanceof MultiValueInterface) {
            return $this->compareValue($value);
        } elseif ($this->getConfig(static::KEY_ANY_ALL) === 'any') {
            return $this->compareAny($value);
        }

        return $this->compareAll($value);
    }
}
