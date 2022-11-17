<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

class TrimContentResolver extends AbstractModifierContentResolver
{
    protected function modifyValue(&$result)
    {
        $result = trim($result);
    }
}
