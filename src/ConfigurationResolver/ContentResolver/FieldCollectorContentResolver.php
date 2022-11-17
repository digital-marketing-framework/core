<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationBehaviour;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class FieldCollectorContentResolver extends ContentResolver
{
    protected const KEY_EXCLUDE = 'exclude';
    protected const DEFAULT_EXCLUDE = '';

    protected const KEY_IGNORE_IF_EMPTY = 'ignoreIfEmpty';
    protected const DEFAULT_IGNORE_IF_EMPTY = true;

    protected const KEY_UNPROCESSED_ONLY = 'unprocessedOnly';
    protected const DEFAULT_UNPROCESSED_ONLY = true;

    protected const KEY_TEMPLATE = 'template';
    protected const DEFAULT_TEMPLATE = '{key}\s=\s{value}\n';

    protected function getConfigurationBehaviour(): ConfigurationBehaviour
    {
        return ConfigurationBehaviour::IgnoreScalar;
    }

    public function build()
    {
        $exclude = $this->resolveContent($this->getConfig(static::KEY_EXCLUDE));
        $ignoreIfEmpty = $this->evaluate($this->getConfig(static::KEY_IGNORE_IF_EMPTY));
        $unprocessedOnly = $this->evaluate($this->getConfig(static::KEY_UNPROCESSED_ONLY));
        $template = $this->resolveContent($this->getConfig(static::KEY_TEMPLATE));

        $excludedFields = GeneralUtility::castValueToArray($exclude);
        $template = GeneralUtility::parseSeparatorString($template);

        $result = '';
        foreach ($this->context->getData() as $key => $value) {
            if (in_array($key, $excludedFields)) {
                continue;
            }
            if ($ignoreIfEmpty && GeneralUtility::isEmpty($value)) {
                continue;
            }
            if ($unprocessedOnly && $this->context->getFieldTracker()->hasBeenProcessed($key)) {
                continue;
            }
            $part = $template;
            $part = str_replace('{key}', $key, $part);
            $part = str_replace('{value}', $value, $part);
            $result .= $part;
        }
        return $result;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_EXCLUDE => static::DEFAULT_EXCLUDE,
            static::KEY_IGNORE_IF_EMPTY => static::DEFAULT_IGNORE_IF_EMPTY,
            static::KEY_UNPROCESSED_ONLY => static::DEFAULT_UNPROCESSED_ONLY,
            static::KEY_TEMPLATE => static::DEFAULT_TEMPLATE,
        ];
    }
}
