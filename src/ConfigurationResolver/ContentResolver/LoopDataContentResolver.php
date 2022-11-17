<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class LoopDataContentResolver extends ContentResolver
{
    protected const KEY_GLUE = 'glue';
    protected const DEFAULT_GLUE = false;

    protected const KEY_TEMPLATE = 'template';
    protected const DEFAULT_TEMPLATE = '';

    protected const KEY_VAR_KEY = 'asKey';
    protected const DEFAULT_VAR_KEY = 'key';

    protected const KEY_VAR_VALUE = 'as';
    protected const DEFAULT_VAR_VALUE = 'value';

    protected const KEY_CONDITION = 'condition';
    protected const DEFAULT_CONDITION = false;

    public function build(): string|ValueInterface|null
    {
        if (!is_array($this->configuration)) {
            $this->configuration = is_string($this->configuration) ? [static::KEY_TEMPLATE => $this->configuration] : [];
        }

        $glue = $this->getConfig(static::KEY_GLUE);
        $varKey = $this->getConfig(static::KEY_VAR_KEY);
        $varValue = $this->getConfig(static::KEY_VAR_VALUE);

        $template = $this->getConfig(static::KEY_TEMPLATE);
        if (empty($template) || $template === true) {
            $template = [
                ConfigurationResolverInterface::KEY_SELF => '{' . $varKey . '}\s=\s{' . $varValue . '}\n',
                'insertData' => true
            ];
        }

        $condition = $this->getConfig(static::KEY_CONDITION);

        // don't allow overrides of form data
        if ($this->fieldExists($varKey) || $this->fieldExists($varValue)) {
            if ($this->fieldExists($varKey)) {
                $this->logger->error('content-resolver "loop-data": key name "' . $varKey .'" exists as field.');
            }
            if ($this->fieldExists($varValue)) {
                $this->logger->error('content-resolver "loop-data": value name "' . $varValue .'" exists as field.');
            }
            return '';
        }

        $result = [];
        if ($glue) {
            $result[static::KEYWORD_GLUE] = $glue;
        }
        foreach ($this->context->getData() as $key => $value) {
            if ($condition) {
                $context = $this->context->copy();
                $this->addKeyToContext($key, $context);
                if (!$this->evaluate($condition, $context)) {
                    continue;
                }
            }

            $context = $this->context->copy();
            $context->getData()[$varKey] = $key;
            $context->getData()[$varValue] = $value;
            $result[] = $this->resolveContent($template, $context);
            unset($context->getData()[$varKey]);
            unset($context->getData()[$varValue]);
        }
        return $this->resolveContent($result);
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_GLUE => static::DEFAULT_GLUE,
            static::KEY_TEMPLATE => static::DEFAULT_TEMPLATE,
            static::KEY_VAR_KEY => static::DEFAULT_VAR_KEY,
            static::KEY_VAR_VALUE => static::DEFAULT_VAR_VALUE,
            static::KEY_CONDITION => static::DEFAULT_CONDITION,
        ];
    }
}
