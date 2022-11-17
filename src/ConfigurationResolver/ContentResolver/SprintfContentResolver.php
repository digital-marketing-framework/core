<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

class SprintfContentResolver extends AbstractModifierContentResolver
{

    protected function modify(&$result)
    {
        if ($result instanceof MultiValue) {
            $values = $result->toArray();
        } else {
            $values = [$result];
        }
        $result = sprintf($this->configuration, ...$values);
    }
}
