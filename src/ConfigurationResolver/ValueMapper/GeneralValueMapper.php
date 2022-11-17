<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationBehaviour;
use DigitalMarketingFramework\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class GeneralValueMapper extends ValueMapper implements GeneralConfigurationResolverInterface
{
    protected function getConfigurationBehaviour(): ConfigurationBehaviour
    {
        return ConfigurationBehaviour::ConvertScalarToArrayWithSelfValue;
    }

    protected function resolveValue(string|ValueInterface|null $fieldValue): string|ValueInterface|null
    {
        $valueMappers = [];
        foreach ($this->configuration as $key => $value) {
            // try to instantiate sub-mapper
            $valueMapper = $this->resolveKeyword($key, $value);

            // if not successful, create a general mapper as sub-mapper if the config key is the data value
            if (!$valueMapper && (string)$key === (string)$fieldValue) {
                $valueMapper = $this->resolveKeyword('general', $value);
            }

            if ($valueMapper) {
                $valueMappers[] = $valueMapper;
            }
        }

        $this->sortSubResolvers($valueMappers);

        foreach ($valueMappers as $valueMapper) {
            // calculate the result
            $result = $valueMapper->resolve($fieldValue);
            // if the result is not null (may be returned from an evaluation process without a then/else part)
            // then stop and return the result
            if ($result !== null) {
                return $result;
            }
        }

        // if no result was found, return the original value
        return parent::resolveValue($fieldValue);
    }
}
