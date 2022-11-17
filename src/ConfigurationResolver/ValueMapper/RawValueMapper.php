<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class RawValueMapper extends ValueMapper
{
    public function resolveValue(string|ValueInterface|null $fieldValue): string|ValueInterface|null
    {
        $stringFieldValue = (string)$fieldValue;
        if (isset($this->configuration[$stringFieldValue])) {
            /** @var GeneralValueMapper $valueMapper */
            $valueMapper = $this->resolveKeyword('general', $this->configuration[$stringFieldValue]);
            $result = $valueMapper->resolve($fieldValue);
            if ($result !== null) {
                return $result;
            }
        }
        return parent::resolveValue($fieldValue);
    }
}
