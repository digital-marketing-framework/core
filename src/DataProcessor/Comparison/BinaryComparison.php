<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class BinaryComparison extends Comparison
{
    public const KEY_FIRST_OPERAND = 'firstOperand';

    public const DEFAULT_FIRST_OPERAND = null;

    public const KEY_SECOND_OPERAND = 'secondOperand';

    public const DEFAULT_SECOND_OPERAND = null;

    abstract protected function compareValues(string|null|ValueInterface $a, string|null|ValueInterface $b): bool;

    protected function compareAny(MultiValueInterface $a, string|null|ValueInterface $b): bool
    {
        if (GeneralUtility::isEmpty($a)) {
            return $this->compareAnyEmpty();
        }

        foreach ($a as $subValue) {
            if ($this->compareValues($subValue, $b)) {
                return true;
            }
        }

        return false;
    }

    protected function compareAll(MultiValueInterface $a, string|null|ValueInterface $b): bool
    {
        if (GeneralUtility::isEmpty($a)) {
            return $this->compareAllEmpty();
        }

        foreach ($a as $subValue) {
            if (!$this->compareValues($subValue, $b)) {
                return false;
            }
        }

        return true;
    }

    public function compare(): bool
    {
        $a = $this->dataProcessor->processValue($this->getConfig(static::KEY_FIRST_OPERAND), $this->context->copy());
        $b = $this->dataProcessor->processValue($this->getConfig(static::KEY_SECOND_OPERAND), $this->context->copy());
        if (!static::handleMultiValuesIndividually() || !$a instanceof MultiValueInterface) {
            return $this->compareValues($a, $b);
        } elseif ($this->getConfig(static::KEY_ANY_ALL) === 'any') {
            return $this->compareAny($a, $b);
        }

        return $this->compareAll($a, $b);
    }
}
