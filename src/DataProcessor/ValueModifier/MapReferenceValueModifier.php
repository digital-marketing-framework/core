<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class MapReferenceValueModifier extends ValueModifier
{
    public const WEIGHT = 40;

    public const KEY_MAP_NAME = 'reference';
    public const DEFAULT_MAP_NAME = '';

    public const KEY_INVERT = 'invert';
    public const DEFAULT_INVERT = false;

    protected function modifyValue(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if ($value === null) {
            return null;
        }
        $map = $this->context->getConfiguration()->getValueMapConfiguration($this->getConfig(static::KEY_MAP_NAME));
        if (is_array($map)) {
            if ($this->getConfig(static::KEY_INVERT)) {
                $map = array_flip($map);
            }
            return $map[(string)$value] ?? $value;
        }
        return $value;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_MAP_NAME => static::DEFAULT_MAP_NAME,
            static::KEY_INVERT => static::DEFAULT_INVERT,
        ];
    }
}
