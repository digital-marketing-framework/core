<?php

namespace DigitalMarketingFramework\Core\DataProcessor\ValueModifier;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class SpliceValueModifier extends ValueModifier
{
    public const KEY_TOKEN = 'token';
    public const DEFAULT_TOKEN = '\\s';

    public const KEY_INDEX = 'index';
    public const DEFAULT_INDEX = '1';

    protected function modifyValue(null|string|ValueInterface $value): null|string|ValueInterface
    {
        if ($value === null) {
            return null;
        }

        $token = GeneralUtility::parseSeparatorString($this->getConfig(static::KEY_TOKEN));
        $slice = $this->getConfig(static::KEY_INDEX);
        $indices = explode(':', $slice);

        $offset = $indices[0] ?: 1;
        if ($offset > 0) {
            $offset--;
        }
        if (count($indices) === 1) {
            // '' || 'X'
            $length = 1;
        } else {
            // 'X:' || ':Y' || 'X:Y'
            $length = $indices[1] ?: null;
        }
        $parts = explode($token, (string)$value);
        if ($length === null) {
            $slices = array_slice($parts, $offset);
        } else {
            $slices = array_slice($parts, $offset, $length);
        }
        return implode($token, $slices);
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_TOKEN => static::DEFAULT_TOKEN,
            static::KEY_INDEX => static::DEFAULT_INDEX,
        ];
    }
}
