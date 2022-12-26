<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class SwitchValueMapper extends ValueMapper
{
    protected const KEY_CASE = 'case';
    protected const KEY_VALUE = 'value';

    /**
     * @param string|ValueInterface|null $fieldValue
     * @return string|ValueInterface|null
     */
    public function resolveValue(string|ValueInterface|null $fieldValue): string|ValueInterface|null
    {
        $stringFieldValue = (string)$fieldValue;
        $valueMapper = null;
        ksort($this->configuration, SORT_NUMERIC);
        foreach ($this->configuration as $case) {
            $caseValue = $case[static::KEY_CASE] ?? ($case[ConfigurationResolverInterface::KEY_SELF] ?? '');
            $caseResult = $case[static::KEY_VALUE] ?? '';
            if ((string)$caseValue === $stringFieldValue) {
                /** @var GeneralValueMapper $valueMapper */
                $valueMapper = $this->resolveKeyword('general', $caseResult);
                break;
            }
        }
        if ($valueMapper) {
            return $valueMapper->resolve($fieldValue);
        }
        return parent::resolveValue($fieldValue);
    }
}
