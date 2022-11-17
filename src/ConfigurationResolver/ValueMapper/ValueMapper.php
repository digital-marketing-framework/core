<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolver;
use DigitalMarketingFramework\Core\Model\Form\FieldInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

abstract class ValueMapper extends ConfigurationResolver implements ValueMapperInterface
{
    protected static function getResolverInterface(): string
    {
        return ValueMapperInterface::class;
    }

    protected function resolveValue($fieldValue)
    {
        return $fieldValue;
    }

    /**
     * @param string|FieldInterface|null $fieldValue
     * @return string|FieldInterface|null
     */
    public function resolve($fieldValue = null)
    {
        // TODO this fallback on fieldValues should not be necessary anymore. confirm and then remove
        if ($fieldValue === null) {
            $fieldValue = $this->getSelectedValue();
        }

        if ($fieldValue === null) {
            return null;
        }

        if ($fieldValue instanceof MultiValue) {
            $result = [];
            foreach ($fieldValue as $key => $value) {
                $result[$key] = $this->resolve($value);
            }
            $class = get_class($fieldValue);
            return new $class($result);
        }
        return $this->resolveValue($fieldValue);
    }
}
