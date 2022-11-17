<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class LowerCaseContentResolver extends AbstractModifierContentResolver
{
    protected function modifyValue(string|ValueInterface|null &$result): void
    {
        $result = strtolower($result);
    }
}
