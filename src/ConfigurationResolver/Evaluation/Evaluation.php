<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class Evaluation extends ConfigurationResolver implements EvaluationInterface
{
    protected static function getResolverInterface(): string
    {
        return EvaluationInterface::class;
    }

    protected function addModifierToContext($modifier, $context = null)
    {
        if ($context === null) {
            $context = $this->context;
        }
        if (is_array($modifier)) {
            foreach ($modifier as $modifierKey => $modifierValue) {
                $context['modifier'][$modifierKey] = $modifierValue;
            }
        } else {
            $modifiers = GeneralUtility::castValueToArray($modifier);
            foreach ($modifiers as $modifierKey) {
                $context['modifier'][$modifierKey] = true;
            }
        }
    }

    /**
     * @param string|ValueInterface|null $fieldValue
     * @return string|ValueInterface|null
     */
    protected function modifyValue($fieldValue)
    {
        $modifierConfig = $this->context['modifier'] ?? null;
        if ($modifierConfig) {
            $modifierConfig[ConfigurationResolverInterface::KEY_SELF] = $fieldValue;
            $fieldValue = $this->resolveContent($modifierConfig);
        }
        return $fieldValue;
    }

    protected function getSelectedValue($context = null)
    {
        return $this->modifyValue(parent::getSelectedValue($context));
    }

    /**
     * @param string|FieldInterface|null $fieldValue
     * @return bool
     */
    protected function evalValue($fieldValue)
    {
        return true;
    }

    protected function evalMultiValue(MultiValue $fieldValue): bool
    {
        return $this->evalValue($fieldValue);
    }

    /**
     * the method "eval" is called to evaluate the expression defined in the config
     * it will always return a boolean value
     *
     * @return bool
     */
    public function eval(): bool
    {
        $fieldValue = $this->getSelectedValue();

        if ($fieldValue instanceof MultiValue) {
            $result = $this->evalMultiValue($fieldValue);
        } else {
            $result = $this->evalValue($fieldValue);
        }
        return $result;
    }
}
