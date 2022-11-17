<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

abstract class AbstractModifierContentResolver extends ContentResolver
{
    protected const WEIGHT = 20;

    protected function modifyValue(&$result)
    {
    }

    protected function modify(&$result)
    {
        if ($result instanceof MultiValue) {
            foreach ($result as $key => $value) {
                $this->modify($result[$key]);
            }
        } else {
            $this->modifyValue($result);
        }
    }

    public function finish(&$result): bool
    {
        if ($this->configuration && $result !== null) {
            $this->modify($result);
        }
        return false;
    }
}
