<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;

class MultiValueContentResolver extends ContentResolver
{
    protected function getMultiValue(): MultiValueInterface
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
