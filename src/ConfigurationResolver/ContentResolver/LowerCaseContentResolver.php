<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

class LowerCaseContentResolver extends AbstractModifierContentResolver
{
    protected function modifyValue(&$result)
    {
        $result = strtolower($result);
    }
}
