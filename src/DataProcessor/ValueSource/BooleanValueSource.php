<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\Model\Data\Value\BooleanValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class BooleanValueSource extends ValueSource
{
    public const KEY_VALUE = 'value';
    public const DEFAULT_VALUE = null;

    public const KEY_TRUE = 'true';
    public const DEFAULT_TRUE = '1';

    public const KEY_FALSE = 'false';
    public const DEFAULT_FALSE = '0';

    public function build(): null|string|ValueInterface
    {
        $value = $this->getConfig(static::KEY_VALUE);
        if ($value !== null) {
            $value = $this->dataProcessor->processValue($value, $this->context->copy());
        }
        if ($value === null) {
            return null;
        }
        return new BooleanValue(
            GeneralUtility::isTrue($value),
            $this->getConfig(static::KEY_TRUE),
            $this->getConfig(static::KEY_FALSE)
        );
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_VALUE => static::DEFAULT_VALUE,
            static::KEY_TRUE => static::DEFAULT_TRUE,
            static::KEY_FALSE => static::DEFAULT_FALSE,
        ];
    }

    public static function canBeMultiValue(): bool
    {
        return false;
    }
}
