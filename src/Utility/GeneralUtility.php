<?php

namespace DigitalMarketingFramework\Core\Utility;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

final class GeneralUtility
{
    protected const CHARACTER_MAP = [
        '\\n' => PHP_EOL,
        '\\s' => ' ',
        '\\t' => "\t",
    ];

    public static function isEmpty($value)
    {
        if (is_array($value)) {
            return empty($value);
        }
        if ($value instanceof MultiValue) {
            return empty($value->toArray());
        }
        return strlen((string)$value) === 0;
    }

    public static function isTrue($value)
    {
        if ($value instanceof MultiValue) {
            return (bool)$value->toArray();
        }
        return (bool)$value;
    }

    public static function isFalse($value)
    {
        if ($value instanceof MultiValue) {
            return !$value->toArray();
        }
        return !$value;
    }

    public static function parseSeparatorString($str)
    {
        $str = trim($str);
        foreach (static::CHARACTER_MAP as $key => $value) {
            $str = str_replace($key, $value, $str);
        }
        return $str;
    }

    public static function isList($value): bool
    {
        return is_array($value) || $value instanceof MultiValue;
    }

    public static function castValueToArray($value, $token = ',', $trim = true): array
    {
        if (is_array($value)) {
            $array = $value;
        } elseif ($value instanceof MultiValue) {
            $array = $value->toArray();
        } else {
            $value = (string)$value;
            $array = !static::isEmpty($value) ? explode($token, $value) : [];
        }

        if ($trim) {
            $array = array_map('trim', $array);
        }

        return $array;
    }

    public static function shortenHash(string $hash): string
    {
        if ($hash === 'undefined') {
            return 'undefined';
        }
        return substr($hash, 0, 5);
    }

    public static function calculateHash(array $data, bool $short = false): string
    {
        if (empty($data)) {
            return 'undefined';
        }
        $serialized = serialize($data);
        if (!$serialized) {
            return 'undefined';
        }
        $hash = strtoupper(md5($serialized));
        return $short ? static::shortenHash($hash) : $hash;
    }

    public static function compareValue($fieldValue, $compareValue): bool
    {
        return (string)$fieldValue === (string)$compareValue;
    }

    public static function compareLists($fieldValue, $compareList, bool $strict = false): bool
    {
        $fieldValue = static::castValueToArray($fieldValue);
        $compareList = static::castValueToArray($compareList);

        if (!$strict) {
            sort($fieldValue);
            sort($compareList);
        }

        return $fieldValue === $compareList;
    }

    public static function compare($fieldValue, $compareValue): bool
    {
        if (static::isList($fieldValue) || static::isList($compareValue)) {
            return static::compareLists($fieldValue, $compareValue);
        }
        return static::compareValue($fieldValue, $compareValue);
    }

    public static function findInList($fieldValue, array $list)
    {
        return array_search($fieldValue, $list);
    }

    public static function isInList($fieldValue, array $list): bool
    {
        return in_array($fieldValue, $list);
    }

    public static function getPluginKeyword(string $class, string $interface): string
    {
        $keyword = '';
        $interfaceNamespaceParts = explode('\\', $interface);
        $interfaceName = array_pop($interfaceNamespaceParts);

        $classNamespaceParts = explode('\\', $class);
        $className = array_pop($classNamespaceParts);

        if (substr($className . 'Interface', -strlen($interfaceName)) === $interfaceName) {
            $keyword = lcfirst(substr($className . 'Interface', 0, -strlen($interfaceName)));
        }
        return $keyword;
    }
}
