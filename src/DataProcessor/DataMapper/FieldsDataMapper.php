<?php

namespace DigitalMarketingFramework\Core\DataProcessor\DataMapper;

use DigitalMarketingFramework\Core\Model\Data\DataInterface;

class FieldsDataMapper extends WritingDataMapper
{
    public const KEY_FIELDS = 'fields';
    public const DEFAULT_FIELDS = [];

    protected function map(DataInterface $target)
    {
        $baseContext = $this->context->copy(false);
        foreach ($this->getConfig(static::KEY_FIELDS) as $fieldName => $valueConfig) {
            $context = $baseContext->copy();
            $value = $this->dataProcessor->processValue($valueConfig, $context);
            if ($value !== null) {
                $this->addField($target, $fieldName, $value);
            }
        }
    }

    public static function getDefaultConfiguration(?bool $enabled = null): array
    {
        return parent::getDefaultConfiguration($enabled) + [
            static::KEY_FIELDS => static::DEFAULT_FIELDS,
        ];
    }
}
