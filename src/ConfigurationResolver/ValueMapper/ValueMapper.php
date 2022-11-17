<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

abstract class ValueMapper extends ConfigurationResolver implements ValueMapperInterface
{
    protected static function getResolverInterface(): string
    {
        return ValueMapperInterface::class;
    }

    protected function resolveValue(string|ValueInterface|null $fieldValue): string|ValueInterface|null
    {
        return $fieldValue;
    }

    protected function resolveMultiValue(MultiValueInterface $fieldValue): string|ValueInterface|null
    {
        $result = [];
        foreach ($fieldValue as $key => $value) {
            $result[$key] = $this->resolve($value);
        }
        $class = get_class($fieldValue);
        return new $class($result);
    }

    public function resolve(string|ValueInterface|null $fieldValue = null): string|ValueInterface|null
    {
        // TODO this fallback on fieldValues should not be necessary anymore. confirm and then remove
        if ($fieldValue === null) {
            $fieldValue = $this->getSelectedValue();
        }

        if ($fieldValue === null) {
            return null;
        }

        if ($fieldValue instanceof MultiValueInterface) {
            return $this->resolveMultiValue($fieldValue);
        }
        
        return $this->resolveValue($fieldValue);
    }
}
