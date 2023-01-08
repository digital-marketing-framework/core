<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class Evaluation extends ConfigurationResolver implements EvaluationInterface
{
    protected static function getResolverInterface(): string
    {
        return EvaluationInterface::class;
    }

    protected function addModifierToContext($modifier, $context = null): void
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

    protected function modifyValue(string|ValueInterface|null $fieldValue): string|ValueInterface|null
    {
        $modifierConfig = $this->context['modifier'] ?? null;
        if ($modifierConfig) {
            $modifierConfig[ConfigurationResolverInterface::KEY_SELF] = $fieldValue;
            $fieldValue = $this->resolveContent($modifierConfig);
        }
        return $fieldValue;
    }

    protected function getSelectedValue(?ConfigurationResolverContextInterface $context = null): string|ValueInterface|null
    {
        return $this->modifyValue(parent::getSelectedValue($context));
    }

    protected function evalValue(string|ValueInterface|null $fieldValue): bool
    {
        return true;
    }

    protected function evalMultiValue(MultiValueInterface $fieldValue): bool
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

        if ($fieldValue instanceof MultiValueInterface) {
            $result = $this->evalMultiValue($fieldValue);
        } else {
            $result = $this->evalValue($fieldValue);
        }
        return $result;
    }
}
