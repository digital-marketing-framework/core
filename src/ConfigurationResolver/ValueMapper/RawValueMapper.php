<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class RawValueMapper extends ValueMapper
{
    public function resolveValue(string|ValueInterface|null $fieldValue): string|ValueInterface|null
    {
        if (isset($this->configuration[$fieldValue])) {
            /** @var GeneralValueMapper $valueMapper */
            $valueMapper = $this->resolveKeyword('general', $this->configuration[$fieldValue]);
            $result = $valueMapper->resolve($fieldValue);
            if ($result !== null) {
                return $result;
            }
        }
        return parent::resolveValue($fieldValue);
    }
}
