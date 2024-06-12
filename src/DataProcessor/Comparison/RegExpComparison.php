<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class RegExpComparison extends BinaryComparison
{
    public static function getLabel(): ?string
    {
        return 'matches regular expression';
    }

    protected function compareValues(string|ValueInterface|null $a, string|ValueInterface|null $b): bool
    {
        return (bool)preg_match('/' . trim((string)$b, '/') . '/', (string)$a);
    }
}
