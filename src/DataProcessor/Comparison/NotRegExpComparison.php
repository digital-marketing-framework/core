<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class NotRegExpComparison extends RegExpComparison
{
    public static function getLabel(): ?string
    {
        return 'does not match regular expression';
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
