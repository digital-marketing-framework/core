<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

class FieldContentResolver extends ContentResolver
{
    protected const WEIGHT = 0;

    public function build()
    {
        $fieldName = $this->resolveContent($this->configuration);
        if ($fieldName) {
            return $this->getFieldValue($fieldName);
        }
        return null;
    }
}
