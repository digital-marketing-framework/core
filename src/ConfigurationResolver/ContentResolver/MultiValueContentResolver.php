<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

class MultiValueContentResolver extends ContentResolver
{
    protected function getMultiValue(): MultiValue
    {
        return new MultiValue([]);
    }

    public function build()
    {
        $result = $this->getMultiValue();
        foreach ($this->configuration as $key => $valueConfiguration) {
            $value = $this->resolveContent($valueConfiguration);
            if ($value !== null) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
