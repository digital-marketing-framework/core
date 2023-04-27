<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DefaultValueModifier extends ValueModifier
{
    public const WEIGHT = 100;

    public const KEY_VALUE = 'value';
    public const DEFAULT_VALUE = '';
    
    public function modify(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if (!$this->proceed()) {
            return $value;
        }
        if (GeneralUtility::isEmpty($value)) {
            return $this->getConfig(static::KEY_VALUE);
        }
        return $value;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_VALUE => static::DEFAULT_VALUE,
        ];
    }
}
