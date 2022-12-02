<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class SprintfContentResolver extends AbstractModifierContentResolver
{

    protected function modify(string|ValueInterface|null &$result): void
    {
        if ($result instanceof MultiValue) {
            $values = $result->toArray();
        } else {
            $values = [$result];
        }
        $result = sprintf($this->configuration, ...$values);
    }
}
