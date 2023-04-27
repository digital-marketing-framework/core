<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class ConcatenationValueSource extends ValueSource
{
    public const WEIGHT = 3;

    public const KEY_GLUE = 'glue';
    public const DEFAULT_GLUE = '\\s';

    public const KEY_VALUES = 'values';
    public const DEFAULT_VALUES = [];

    public function build(): null|string|ValueInterface
    {
        $glue = GeneralUtility::parseSeparatorString($this->getConfig(static::KEY_GLUE));
        
        $values = [];
        foreach ($this->getConfig(static::KEY_VALUES) as $valueConfig) {
            $value = $this->dataProcessor->processValue($valueConfig, $this->context->copy());
            if ($value !== null) {
                $values[] = $value;
            }
        }
        if (empty($values)) {
            return null;
        }
        if (count($values) === 1) {
            return reset($values);
        }
        return implode($glue, $values);
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_GLUE => static::DEFAULT_GLUE,
            static::KEY_VALUES => static::DEFAULT_VALUES,
        ];
    }

    public static function canBeMultiValue(): bool
    {
        return false;
    }
}
