<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class NotExistsComparison extends ExistsComparison
{
    public static function getLabel(): ?string
    {
        return 'does not exist';
    }

    protected function compareAnyEmpty(bool $secondOperandEmpty = true): bool
    {
        return !parent::compareAnyEmpty($secondOperandEmpty);
    }

    protected function compareAllEmpty(bool $secondOperandEmpty = true): bool
    {
        return !parent::compareAllEmpty($secondOperandEmpty);
    }

    protected function compareValue(string|ValueInterface|null $value): bool
    {
        return !parent::compareValue($value);
    }
}
