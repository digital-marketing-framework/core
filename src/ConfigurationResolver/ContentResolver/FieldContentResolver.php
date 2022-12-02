<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class FieldContentResolver extends ContentResolver
{
    protected const WEIGHT = 0;

    public function build(): string|ValueInterface|null
    {
        $fieldName = $this->resolveContent($this->configuration);
        if ($fieldName) {
            return $this->getFieldValue($fieldName);
        }
        return null;
    }
}
