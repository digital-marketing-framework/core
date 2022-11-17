<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\Model\Form\FieldInterface;

class RawValueMapper extends ValueMapper
{
    /**
     * @param string|FieldInterface|null $fieldValue
     * @return string|FieldInterface|null
     */
    public function resolveValue($fieldValue)
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
