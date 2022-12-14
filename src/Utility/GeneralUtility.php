<?php

namespace DigitalMarketingFramework\Core\Utility;

use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;

final class GeneralUtility
{
    protected const CHARACTER_MAP = [
        '\\n' => PHP_EOL,
        '\\s' => ' ',
        '\\t' => "\t",
    ];

    public static function isEmpty($value): bool
    {
        if (is_array($value)) {
            return empty($value);
        }
        if ($value instanceof MultiValueInterface) {
            return empty($value->toArray());
        }
        return strlen((string)$value) === 0;
    }

    public static function isTrue($value): bool
    {
        if ($value instanceof MultiValueInterface) {
            return (bool)$value->toArray();
        }
        return (bool)$value;
    }

    public static function isFalse($value): bool
    {
        if ($value instanceof MultiValueInterface) {
            return !$value->toArray();
        }
        return !$value;
    }

    public static function parseSeparatorString($str): string
    {
        $str = trim($str);
        foreach (static::CHARACTER_MAP as $key => $value) {
            $str = str_replace($key, $value, $str);
        }
        return $str;
    }

    public static function isList($value): bool
    {
        return is_array($value) || $value instanceof MultiValueInterface;
    }

    public static function castValueToArray($value, $token = ',', $trim = true): array
    {
        if (is_array($value)) {
            $array = $value;
        } elseif ($value instanceof MultiValueInterface) {
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

    public static function castArrayToMultiValue(array $array): MultiValueInterface
    {
        $multiValue = new MultiValue();
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                $multiValue[$key] = $value;
            } else {
                $multiValue[$key] = static::castArrayToMultiValue($value);
            }
        }
        return $multiValue;
    }

    public static function castArrayToData(array $array): DataInterface
    {
        $data = new Data();
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                $data[$key] = $value;
            } else {
                $data[$key] = static::castArrayToMultiValue($value);
            }
        }
        return $data;
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

    public static function findInList($fieldValue, array $list): string|int|false
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

    public static function maskValue(string $value): string
    {
        if (preg_match('/@/', $value)) {
            $parts = explode('@', $value);

            $firstPart = array_shift($parts);
            $lengthFirstPart = strlen($firstPart);
            $maskedFirstPart = (int)ceil($lengthFirstPart / 2);

            $secondPart = implode('@', $parts);
            $lengthSecondPart = strlen($secondPart);
            $maskedSecondPart = (int)ceil($lengthSecondPart / 2);

            return substr($firstPart, 0, $lengthFirstPart - $maskedFirstPart)
            //    . str_repeat('*', $maskedFirstPart)
            //    . '*'
            //    . str_repeat('*', $maskedSecondPart)
                . '****'
                . substr($secondPart, $maskedSecondPart);
        }
        $length = strlen($value);
        $masked = (int)ceil($length / 2);
        $start = (int)floor($masked / 2);
        return substr($value, 0, $start) . str_repeat('*', 4) . substr($value, $start + $masked);
    }
}
