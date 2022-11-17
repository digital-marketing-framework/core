<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class TrimContentResolver extends AbstractModifierContentResolver
{
    protected function modifyValue(string|ValueInterface|null &$result): void
    {
        $result = trim($result);
    }
}
