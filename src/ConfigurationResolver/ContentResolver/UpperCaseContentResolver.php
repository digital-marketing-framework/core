<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

class UpperCaseContentResolver extends AbstractModifierContentResolver
{
    protected function modifyValue(&$result)
    {
        $result = strtoupper($result);
    }
}
