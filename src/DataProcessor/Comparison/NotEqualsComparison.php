<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class NotEqualsComparison extends EqualsComparison
{
    public static function getLabel(): ?string
    {
        return 'does not equal';
    }

    protected function compareAnyEmpty(bool $secondOperandEmpty = true): bool
    {
        return !parent::compareAnyEmpty($secondOperandEmpty);
    }

    protected function compareAllEmpty(bool $secondOperandEmpty = true): bool
    {
        return !parent::compareAllEmpty($secondOperandEmpty);
    }

    protected function compareValues(string|ValueInterface|null $a, string|ValueInterface|null $b): bool
    {
        return !parent::compareValues($a, $b);
    }
}
