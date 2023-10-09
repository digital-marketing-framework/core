<?php

namespace DigitalMarketingFramework\Core\DataProcessor\Comparison;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class RegExpComparison extends BinaryComparison
{
    protected function compareValues(string|null|ValueInterface $a, string|null|ValueInterface $b): bool
    {
        return preg_match('/' . trim((string)$b, '/') . '/', (string)$a);
    }
}
